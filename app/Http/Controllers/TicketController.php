<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // pastikan sudah ter-install barryvdh/laravel-dompdf

class TicketController extends Controller
{
    public function download(Ticket $ticket)
    {
        // 1) Buat URL QR code dinamis
        $qrUrl = 'https://chart.googleapis.com/chart'
            . '?cht=qr'
            . '&chs=150x150'
            . '&chl=' . urlencode(route('tickets.download', $ticket->id));

        // 2) Render PDF, kirim $ticket + $qrUrl ke view
        $pdf = Pdf::loadView('tickets.pdf', [
            'ticket' => $ticket,
            'qrUrl' => $qrUrl,
        ])
            // Aktifkan remote image agar Google Charts bisa di-fetch
            ->setOptions([
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download("ticket-{$ticket->id}.pdf");
    }
}