<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;
use App\Filament\Resources\PaymentResource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Grid;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Filament\Infolists\Components\Actions\Action as InfolistAction;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Payment Information')
                    ->schema([
                        TextEntry::make('transaction_id')
                            ->label('Transaction ID'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('IDR'),
                        TextEntry::make('method')
                            ->label('Method')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'midtrans' => 'info',
                                'xendit' => 'warning',
                                'manual' => 'gray',
                                'cash' => 'success',
                                'transfer' => 'primary',
                                'qris' => 'secondary',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'expired' => 'gray',
                                'refunded' => 'info',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('paid_at')
                            ->label('Paid At')
                            ->dateTime('d M Y H:i')
                            ->visible(fn($record) => $record->paid_at),
                    ])
                    ->columns(2),

                Section::make('Buyer Information')
                    ->schema([
                        TextEntry::make('buyer_name')
                            ->label('Name'),
                        TextEntry::make('buyer_email')
                            ->label('Email'),
                        TextEntry::make('buyer_phone')
                            ->label('Phone'),
                    ])
                    ->columns(3),

                Section::make('Related Tickets')
                    ->description('Tickets associated with this payment')
                    ->schema([
                        RepeatableEntry::make('tickets')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('id')
                                            ->label('Ticket ID')
                                            ->badge()
                                            ->color('primary'),
                                        TextEntry::make('event.title')
                                            ->label('Event')
                                            ->limit(50)
                                            ->badge()
                                            ->weight('bold'),
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('participant_name')
                                            ->label('Participant')
                                            ->weight('medium'),
                                        TextEntry::make('participant_email')
                                            ->label('Email')
                                            ->weight('medium'),
                                        TextEntry::make('participant_phone')
                                            ->label('Phone')
                                            ->weight('medium'),
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('price_paid')
                                            ->label('Price')
                                            ->money('IDR')
                                            ->weight('medium'),
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'pending' => 'warning',
                                                'paid' => 'success',
                                                'cancelled' => 'danger',
                                                'used' => 'gray',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('created_at')
                                            ->label('Created At')
                                            ->dateTime('d M Y H:i')
                                            ->weight('medium'),
                                    ]),
                                ImageEntry::make('qr_code')
                                    ->label('QR Code')
                                    ->disk('public')
                                    ->height(200)
                                    ->width(200)
                                    ->defaultImageUrl(url('/images/no-qr.png'))
                                    ->extraAttributes(['style' => 'justify-content: center; margin-bottom: 10px;'])
                                    ->state(fn($record) => route('tickets.qr-code', ['ticket' => $record->id])),
                            ])
                            ->columns(1)
                    ])
                    ->collapsible(),

                Section::make('Receipt')
                    ->schema([
                        ImageEntry::make('receipt_path')
                            ->label('')
                            ->disk('public')
                            ->height(300)
                            ->width(300)
                            ->extraAttributes(['style' => 'justify-content: center; margin-bottom: 10px;'])
                            ->defaultImageUrl(url('/images/eventgratis.png')),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getActions(): array
    {
        return [
            // Action::make('preview_qr')
            //     ->modalContent(fn ($record) => view('components.qr-code-preview', [
            //         'ticket' => $record,
            //         'qrBase64' => base64_encode((new PngWriter())
            //             ->write(new QrCode(route('tickets.download', $record->id)))
            //             ->getString())
            //     ])),
            // Action::make('markAsPaid')
            //     ->label('Mark as Paid')
            //     ->icon('heroicon-o-check-circle')
            //     ->color('success')
            //     ->visible(fn() => $this->record->status === 'pending')
            //     ->action(fn() => $this->record->update(['status' => 'paid'])),

            // Action::make('refund')
            //     ->label('Refund')
            //     ->icon('heroicon-o-currency-dollar')
            //     ->color('danger')
            //     ->visible(fn() => $this->record->status === 'paid')
            //     ->action(fn() => $this->record->update(['status' => 'failed'])),
        ];
    }
}
