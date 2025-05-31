<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Resources\Resource;
use Filament\Tables\Table as FilamentTable;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\PaymentResource\Pages\ListPayments;
use App\Filament\Resources\PaymentResource\Pages\ViewPayment;
use Filament\Forms\Form as FilamentForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Payment Management';
    protected static ?string $navigationLabel = 'Payments';
    protected static ?int $navigationSort = 3;

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form
            ->schema([
                Select::make('ticket_reference')
                    ->label('Ticket')
                    ->relationship('ticket', 'id')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Select::make('method')
                    ->label('Payment Method')
                    ->options([
                        'midtrans' => 'Midtrans',
                        'xendit' => 'Xendit',
                        'manual' => 'Manual',
                        'cash' => 'Cash',
                        'transfer' => 'Bank Transfer',
                        'qris' => 'QRIS',
                    ])
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'refunded' => 'Refunded',
                    ])
                    ->default('pending')
                    ->required(),

                TextInput::make('transaction_id')
                    ->label('Transaction ID')
                    ->maxLength(255),

                TextInput::make('payment_url')
                    ->label('Payment URL')
                    ->url()
                    ->maxLength(255),
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                TextColumn::make('ticket.id')
                    ->label('Ticket ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('ticket.event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('buyer_name')
                    ->label('Pemesan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('method')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'midtrans' => 'info',
                        'xendit' => 'warning',
                        'manual' => 'gray',
                        'cash' => 'success',
                        'transfer' => 'primary',
                        'qris' => 'secondary',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\ImageColumn::make('receipt_path')
                    ->label('Bukti')
                    ->disk('public')
                    ->height(60)
                    ->width(60)
                    ->defaultImageUrl(url('/images/eventgratis.png')),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'gray' => 'expired',
                        'info' => 'refunded',
                    ]),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'refunded' => 'Refunded',
                    ]),

                SelectFilter::make('method')
                    ->label('Method')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Bank Transfer',
                        'qris' => 'QRIS',
                    ]),

                Filter::make('date')
                    ->label('Date Range')
                    ->form([
                        DatePicker::make('created_from')->label('From'),
                        DatePicker::make('created_until')->label('To'),
                    ])
                    ->query(
                        fn($query, array $data) => $query
                            ->when($data['created_from'], fn($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn($q) => $q->whereDate('created_at', '<=', $data['created_until']))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->button()
                    ->extraAttributes(['class' => 'bg-khb-blue hover:bg-khb-blue/80']),

                Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Refund')
                    ->modalDescription('Apakah Anda yakin ingin melakukan refund untuk pembayaran ini?')
                    ->modalSubmitActionLabel('Ya, Refund')
                    ->modalCancelActionLabel('Batal')
                    ->visible(fn (Payment $record): bool => $record->status === 'paid')
                    ->action(function (Payment $record): void {
                        $record->update(['status' => 'refunded']);
                        
                        if ($record->tickets) {
                            $record->tickets()->update(['status' => 'cancelled']);
                            
                            $ticketCount = $record->tickets()->count();
                            if ($ticketCount > 0) {
                                $firstTicket = $record->tickets()->first();
                                if ($firstTicket && $firstTicket->event) {
                                    $firstTicket->event->increment('quota', $ticketCount);
                                }
                            }
                        }

                        Notification::make()
                            ->title('Pembayaran berhasil di-refund')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'view' => ViewPayment::route('/{record}'),
        ];
    }
}
