<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PaymentDownloadController extends Controller
{
    public function __invoke(Payment $payment)
    {
        // Load the tickets with their event
        $payment->load(['tickets.event']);
        
        // Check if any of the tickets belong to the current user
        $userHasAccess = $payment->tickets->contains('user_id', auth()->id());
        
        if (!$userHasAccess) {
            abort(403, 'You do not have access to this payment.');
        }

        // Generate QR codes for each ticket
        foreach ($payment->tickets as $ticket) {
            $qrData = route('tickets.download', $ticket->id);
            $qr = new QrCode($qrData);
            $writer = new PngWriter();
            $qrResult = $writer->write($qr);
            $ticket->qr_code = base64_encode($qrResult->getString());
        }

        $pdf = Pdf::loadView('payments.pdf', [
            'payment' => $payment
        ]);

        return $pdf->download("payment-{$payment->transaction_id}.pdf");
    }
} 