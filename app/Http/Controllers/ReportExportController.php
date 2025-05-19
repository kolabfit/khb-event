<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class ReportExportController extends Controller
{
    public function export(Request $request): StreamedResponse
    {
        try {
            $fileName = 'laporan_'.now()->format('Ymd_His').'.csv';

            return response()->streamDownload(function () use ($request) {
                $handle = fopen('php://output', 'w');
                
                if ($handle === false) {
                    throw new \Exception("Cannot open output stream for CSV export");
                }
                
                // header CSV
                fputcsv($handle, ['Ticket ID','Event','User','Amount','Method','Status','Date']);

                // base query
                $query = Payment::with(['ticket.event','ticket.user'])
                    ->orderBy('created_at','desc');

                // apply filter dari querystring
                if ($timePeriod = $request->input('time_period')) {
                    switch ($timePeriod) {
                        case 'today':
                            $query->whereDate('created_at', today());
                            break;
                        case 'yesterday':
                            $query->whereDate('created_at', today()->subDay());
                            break;
                        case 'this_week':
                            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                            break;
                        case 'last_week':
                            $query->whereBetween('created_at', [
                                now()->subWeek()->startOfWeek(),
                                now()->subWeek()->endOfWeek()
                            ]);
                            break;
                        case 'this_month':
                            $query->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year);
                            break;
                        case 'last_month':
                            $query->whereMonth('created_at', now()->subMonth()->month)
                                ->whereYear('created_at', now()->subMonth()->year);
                            break;
                        case 'this_year':
                            $query->whereYear('created_at', now()->year);
                            break;
                    }
                }
                
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
                
                if ($event_id = $request->input('event_id')) {
                    $query->whereHas('ticket', fn($q) => $q->where('event_id', $event_id));
                }

                // tulis tiap baris
                $query->cursor()->each(function ($payment) use ($handle) {
                    if ($payment->ticket && $payment->ticket->event && $payment->ticket->user) {
                        fputcsv($handle, [
                            $payment->ticket->id,
                            $payment->ticket->event->title,
                            $payment->ticket->user->name,
                            $payment->amount,
                            $payment->method,
                            $payment->status,
                            $payment->created_at->format('d-m-Y H:i'),
                        ]);
                    }
                });
                
                fclose($handle);
            }, $fileName, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            ]);
        } catch (\Exception $e) {
            Log::error('CSV Export failed: ' . $e->getMessage());
            return response()->streamDownload(function () {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Error exporting data']);
                fclose($handle);
            }, 'error.csv', [
                'Content-Type' => 'text/csv',
            ]);
        }
    }
}
