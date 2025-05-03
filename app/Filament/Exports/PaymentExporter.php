<?php

namespace App\Filament\Exports;

use Filament\Exports\Concerns\FromQuery;
use Filament\Exports\Concerns\WithMapping;
use Filament\Exports\Concerns\WithHeadings;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Payment;

class PaymentExporter implements FromQuery, WithMapping, WithHeadings
{
    /**
     * Query dasar untuk export. 
     * Harus static supaya Filament dapat memanggilnya tanpa instansiasi.
     */
    public static function query(): Builder
    {
        return Payment::query()
            ->with(['ticket.event', 'ticket.user'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Baris header CSV
     */
    public function headings(): array
    {
        return [
            'Ticket ID',
            'Event Title',
            'User Name',
            'Amount',
            'Method',
            'Status',
            'Created At',
        ];
    }

    /**
     * Map setiap record pembayaran ke array sesuai headings().
     */
    public function map($payment): array
    {
        return [
            $payment->ticket->id,
            $payment->ticket->event->title,
            $payment->ticket->user->name,
            $payment->amount,
            $payment->method,
            $payment->status,
            $payment->created_at->format('d-m-Y H:i'),
        ];
    }
}
