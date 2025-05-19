<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // pastikan sudah ter-install barryvdh/laravel-dompdf
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;

class TicketController extends Controller
{
    public function download(Ticket $ticket)
    {
        // Hanya izinkan download jika status paid atau used
        if (!in_array($ticket->status, ['paid', 'used'])) {
            // Jika request dari Filament (admin), gunakan Filament Notification
            if (request()->routeIs('filament.*')) {
                \Filament\Notifications\Notification::make()
                    ->title('Tiket hanya bisa didownload jika sudah dibayar atau sudah digunakan.')
                    ->danger()
                    ->send();
                return redirect()->back();
            }
            // Jika bukan dari Filament, gunakan laracasts/flash
            \Flash::error('Tiket hanya bisa didownload jika sudah dibayar atau sudah digunakan.');
            return redirect()->back();
        }
        // Load relationships
        $ticket->load(['event', 'user', 'payment']);

        // Generate QR code as base64 using endroid/qr-code
        $qrData = route('tickets.download', $ticket->id);
        $qr = new QrCode($qrData);
        $writer = new PngWriter();
        $qrResult = $writer->write($qr);
        $qrBase64 = base64_encode($qrResult->getString());

        // Ambil URL thumbnail event (pastikan storage:link sudah dibuat)
        $eventThumbnail = $ticket->event->thumbnail;
        // Jika path tidak mengandung http, asumsikan path storage
        if ($eventThumbnail && !str_starts_with($eventThumbnail, 'http')) {
            // Jika path sudah mengandung 'storage/', gunakan asset()
            if (str_starts_with($eventThumbnail, '/storage/')) {
                $eventThumbnail = asset($eventThumbnail);
            } else {
                $eventThumbnail = asset(\Storage::url($eventThumbnail));
            }
        }

        // 2) Render PDF, kirim $ticket + $qrBase64 + $eventThumbnail ke view
        $pdf = Pdf::loadView('tickets.pdf', [
            'ticket' => $ticket,
            'qrBase64' => $qrBase64,
            'eventThumbnail' => $eventThumbnail,
        ])
            // Aktifkan remote image agar Google Charts bisa di-fetch
            ->setOptions([
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download("ticket-{$ticket->id}.pdf");
    }

    public function showQrCode(Ticket $ticket)
    {
        // Generate QR code as base64 using endroid/qr-code
        $qrData = route('tickets.download', $ticket->id);
        $qr = new QrCode($qrData);
        $writer = new PngWriter();
        $qrResult = $writer->write($qr);
        $qrBase64 = base64_encode($qrResult->getString());

        return view('components.qr-code-preview', [
            'ticket' => $ticket,
            'qrBase64' => $qrBase64
        ]);
    }
}