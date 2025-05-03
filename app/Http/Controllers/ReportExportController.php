<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    public function export(Request $request): StreamedResponse
    {
        $fileName = 'laporan_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            // header CSV
            fputcsv($handle, ['Ticket ID','Event','User','Amount','Method','Status','Date']);

            // base query
            $query = Payment::with(['ticket.event','ticket.user'])
                ->orderBy('created_at','desc');

            // apply filter dari querystring
            if ($d = $request->input('created_from')) {
                $query->whereDate('created_at','>=',$d);
            }
            if ($d = $request->input('created_until')) {
                $query->whereDate('created_at','<=',$d);
            }
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }
            if ($cid = $request->input('category_id')) {
                $query->whereHas('ticket.event.categories', fn($q) => $q->where('id',$cid));
            }
            if ($eo = $request->input('eo_id')) {
                $query->whereHas('ticket.event', fn($q) => $q->where('user_id',$eo));
            }

            // tulis tiap baris
            $query->cursor()->each(function ($payment) use ($handle) {
                fputcsv($handle, [
                    $payment->ticket->id,
                    $payment->ticket->event->title,
                    $payment->ticket->user->name,
                    $payment->amount,
                    $payment->method,
                    $payment->status,
                    $payment->created_at->format('d-m-Y H:i'),
                ]);
            });
            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
