<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Response;

class QrCodeController extends Controller
{
    public function generate(Ticket $ticket)
    {
        $qrData = route('tickets.download', $ticket->id);
        $qr = new QrCode($qrData);
        $writer = new PngWriter();
        $result = $writer->write($qr);

        return response($result->getString())
            ->header('Content-Type', 'image/png');
    }
} 