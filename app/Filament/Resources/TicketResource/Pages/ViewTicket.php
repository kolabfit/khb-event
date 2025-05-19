<?php

namespace App\Filament\Resources\TicketResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TicketResource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Storage;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    // (opsional) override judul halaman:
    // protected static ?string $title = 'Detail Tiket';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Event Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('event.title')
                                    ->label('Event Title'),
                                TextEntry::make('event.start_date')
                                    ->label('Event Date')
                                    ->dateTime('d M Y H:i'),
                                TextEntry::make('event.location')
                                    ->label('Location'),
                                TextEntry::make('event.price')
                                    ->label('Ticket Price')
                                    ->formatStateUsing(fn ($state) => (empty($state) || $state == 0) ? 'Gratis' : 'Rp ' . number_format($state, 0, ',', '.')),
                                // TextEntry::make('event.quota')
                                //     ->label('Available Quota'),
                                // TextEntry::make('event.status')
                                //     ->label('Event Status')
                                //     ->badge()
                                //     ->color(fn (string $state): string => match ($state) {
                                //         'draft' => 'gray',
                                //         'pending' => 'warning',
                                //         'approved' => 'success',
                                //         'cancelled' => 'danger',
                                //     }),
                            ]),
                    ]),

                Section::make('Ticket Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Ticket ID'),
                                TextEntry::make('price_paid')
                                    ->label('Amount Paid')
                                    ->formatStateUsing(fn ($state) => (empty($state) || $state == 0) ? 'Gratis' : 'Rp ' . number_format($state, 0, ',', '.')),
                                TextEntry::make('created_at')
                                    ->label('Purchase Date')
                                    ->dateTime('d M Y H:i'),
                                TextEntry::make('status')
                                    ->label('Ticket Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),
                                ImageEntry::make('qr_code')
                                    ->label('QR Code')
                                    ->disk('public')
                                    ->height(200)
                                    ->width(200)
                                    ->defaultImageUrl(url('/images/no-qr.png'))
                                    ->extraAttributes(['style' => 'margin-left: auto; margin-right: auto;'])
                                    ->state(fn ($record) => route('tickets.qr-code', ['ticket' => $record->id])),
                            ]),
                    ]),

                Section::make('Purchaser Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Name'),
                                TextEntry::make('user.email')
                                    ->label('Email'),
                                TextEntry::make('user.phone')
                                    ->label('Phone'),
                            ]),
                    ]),

                Section::make('Participant Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('participant_name')
                                    ->label('Name'),
                                TextEntry::make('participant_email')
                                    ->label('Email'),
                                TextEntry::make('participant_phone')
                                    ->label('Phone'),
                            ]),
                    ]),

                Section::make('Payment Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('payment.transaction_id')
                                    ->label('Transaction ID'),
                                TextEntry::make('payment.status')
                                    ->label('Payment Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('payment.method')
                                    ->label('Payment Method'),
                                TextEntry::make('payment.paid_at')
                                    ->label('Paid At')
                                    ->dateTime('d M Y H:i')
                                    ->visible(fn($record) => $record->payment?->paid_at),
                                ImageEntry::make('payment.receipt_path')
                                    ->label('Payment Proof')
                                    ->visible(fn($record) => $record->payment?->receipt_path)
                                    ->url(fn($record) => Storage::url($record->payment?->receipt_path)),
                            ]),
                    ]),
            ]);
    }
}
