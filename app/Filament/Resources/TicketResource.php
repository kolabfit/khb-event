<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\TicketResource\Pages\ListTickets;
use App\Filament\Resources\TicketResource\Pages\ViewTicket;
use App\Filament\Resources\TicketResource\Pages\CreateTicket;
use App\Filament\Resources\TicketResource\Pages\EditTicket;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\Event;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables\Table as FilamentTable;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use App\Filament\Widgets\ApprovalTicketsWidget;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?string $navigationLabel = 'Tickets';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ticket Creation')
                    ->description('Create tickets for an existing user and event')
                    ->schema([
                        Select::make('user_id')
                            ->label('Select User')
                            ->options(function() {
                                return User::where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function($user) {
                                        return [$user->id => "{$user->name} ({$user->email})"];
                                    });
                            })
                            ->searchable()
                            ->required(),
                        
                        Select::make('event_id')
                            ->label('Select Event')
                            ->options(function() {
                                return Event::whereIn('status', ['draft', 'pending', 'approved'])
                                    ->where('quota', '>', 0)
                                    ->get()
                                    ->mapWithKeys(function($event) {
                                        return [$event->id => "{$event->title} (Quota: {$event->quota})"];
                                    });
                            })
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $event = Event::find($state);
                                $price = $event?->price ?? 0;
                                $set('price_paid', $price);
                                $set('event_price', number_format($price, 0, ',', '.'));
                                $set('event_quota', $event?->quota ?? 0);
                            })
                            ->required(),
                        
                        Grid::make()
                            ->schema([
                                TextInput::make('event_price')
                                    ->label('Ticket Price')
                                    ->disabled()
                                    ->prefix('Rp')
                                    ->default('0')
                                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                        if ($record && $record->event) {
                                            $price = $record->event->price ?? 0;
                                            $component->state(number_format($price, 0, ',', '.'));
                                        } else {
                                            $component->state('0');
                                        }
                                    }),
                                
                                TextInput::make('event_quota')
                                    ->label('Available Quota')
                                    ->disabled()
                                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->event->quota ?? 0);
                                        }
                                    }),
                            ]),
                            
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                // 'admin' => 'Admin Creation',
                                // 'cash' => 'Cash Payment',
                                // 'transfer' => 'Bank Transfer',
                                'qris' => 'QRIS',
                            ])
                            ->default('admin')
                            ->required(),
                        
                        Hidden::make('transaction_id')
                            ->default(fn() => Str::uuid()->toString()),
                    ]),

                Section::make('Participants')
                    ->description('Add one or more participants for this ticket purchase')
                    ->schema([
                        Repeater::make('participants')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('fullname')
                                            ->label('Full Name')
                                            ->required(),
                                        
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required(),
                                        
                                        TextInput::make('phone')
                                            ->label('Phone Number')
                                            ->tel()
                                            ->required(),
                                    ]),
                            ])
                            ->defaultItems(1)
                            ->minItems(1)
                            ->label('Participant Details')
                            ->addActionLabel('Add Participant')
                            ->reorderable(false)
                            ->cloneable(false)
                            ->columns(1)
                    ]),
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Ticket ID')
                    ->sortable(),

                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable(),

                TextColumn::make('participant_name')
                    ->label('Peserta')
                    ->searchable()
                    ->sortable(),
                    
                // TextColumn::make('participant_email')
                //     ->label('Email')
                //     ->searchable()
                //     ->sortable(),

                TextColumn::make('user.name')
                    ->label('Pemesan')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('price_paid')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Gratis' : 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                        'used' => 'Used',
                    ]),

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
                Tables\Actions\ViewAction::make()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Ticket $record) {
                        if (!in_array($record->status, ['paid', 'used'])) {
                            \Filament\Notifications\Notification::make()
                                ->title('Tiket hanya bisa didownload jika sudah dibayar atau sudah digunakan.')
                                ->danger()
                                ->send();
                            return null;
                        }
                        return redirect()->to(route('tickets.download', $record));
                    }),
                // Tables\Actions\Action::make('viewReceipt')
                //     ->label('View Receipt')
                //     ->icon('heroicon-o-document')
                //     ->modalHeading('View Receipt')
                //     ->modalContent(function ($record) {
                //         return view('filament.custom.view-receipt', [
                //             'receiptUrl' => $record->receipt_url,
                //         ]);
                //     })
                //     ->modalSubmitAction(false)
                //     ->modalCancelActionLabel('Tutup'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'edit' => EditTicket::route('/{record}/edit'),
            'view'  => ViewTicket::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ApprovalTicketsWidget::class,
        ];
    }
}       
