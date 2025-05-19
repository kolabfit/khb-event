<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class UserTicketDownloadController extends Controller
{
    public function download(Ticket $ticket)
    {
        // Pastikan user hanya bisa download tiket miliknya sendiri
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses tiket ini.');
        }

        // Load relasi event, user, payment
        $ticket->load(['event', 'user', 'payment']);

        // Generate QR code as base64 using endroid/qr-code
        $qrData = route('tickets.download', $ticket->id);
        $qr = new \Endroid\QrCode\QrCode($qrData);
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $qrResult = $writer->write($qr);
        $qrBase64 = base64_encode($qrResult->getString());

        // Ambil URL thumbnail event (pastikan storage:link sudah dibuat)
        $eventThumbnail = $ticket->event->thumbnail;
        if ($eventThumbnail && !str_starts_with($eventThumbnail, 'http')) {
            if (str_starts_with($eventThumbnail, '/storage/')) {
                $eventThumbnail = asset($eventThumbnail);
            } else {
                $eventThumbnail = asset(\Storage::url($eventThumbnail));
            }
        }

        // Render PDF, kirim $ticket + $qrBase64 + $eventThumbnail ke view
        $pdf = Pdf::loadView('tickets.pdf', [
            'ticket' => $ticket,
            'qrBase64' => $qrBase64,
            'eventThumbnail' => $eventThumbnail,
        ])->setOptions(['isRemoteEnabled' => true]);

        return $pdf->download("ticket-{$ticket->id}.pdf");
    }
} 