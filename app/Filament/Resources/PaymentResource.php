<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Resources\Resource;
use Filament\Tables\Table as FilamentTable;    // ← import yang benar
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
use Filament\Resources\Form;
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
                Select::make('ticket_id')
                    ->label('Ticket')
                    ->relationship('ticket', 'id')
                    ->searchable()
                    ->required(),

                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required(),

                Select::make('method')
                    ->label('Payment Method')
                    ->options([
                        'midtrans' => 'Midtrans',
                        'xendit' => 'Xendit',
                        'manual' => 'Manual',
                    ])
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                TextColumn::make('ticket.id')
                    ->label('Ticket ID')
                    ->sortable(),

                TextColumn::make('ticket.event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ticket.user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn(int $state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('method')
                    ->label('Method')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ]),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),

                SelectFilter::make('method')
                    ->label('Method')
                    ->options(
                        Payment::query()
                            ->distinct()
                            ->pluck('method', 'method')
                            ->toArray()
                    ),

                Filter::make('date')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('created_from')->label('Dari'),
                        DatePicker::make('created_until')->label('Sampai'),
                    ])
                    ->query(
                        fn($query, array $data) => $query
                            ->when($data['created_from'], fn($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn($q) => $q->whereDate('created_at', '<=', $data['created_until']))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('markAsPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Payment $record): bool => $record->status === 'pending')
                    ->action(fn(Payment $record) => $record->update(['status' => 'paid'])),

                Tables\Actions\Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('danger')
                    ->visible(fn(Payment $record): bool => $record->status === 'paid')
                    ->action(fn(Payment $record) => $record->update(['status' => 'failed'])),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
