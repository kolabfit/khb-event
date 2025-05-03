<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;
use App\Filament\Resources\PaymentResource;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('markAsPaid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->status === 'pending')
                ->action(fn() => $this->record->update(['status' => 'paid'])),

            Action::make('refund')
                ->label('Refund')
                ->icon('heroicon-o-currency-dollar')
                ->color('danger')
                ->visible(fn() => $this->record->status === 'paid')
                ->action(fn() => $this->record->update(['status' => 'failed'])),
        ];
    }
}
