<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Payment;
use Inertia\Inertia;

class EventHistoryController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['tickets.event'])
            ->whereHas('tickets', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->get()
            ->map(function ($payment) {
                $event = $payment->tickets->first()?->event;
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'created_at' => $payment->created_at,
                    'receipt_path' => $payment->receipt_path,
                    'event' => [
                        'title' => $event->title ?? '-',
                        'start_date' => $event->start_date ?? null,
                        'end_date' => $event->end_date ?? null,
                        'location' => $event->location ?? '-',
                        'thumbnail_url' => $event->thumbnail ?? null,
                    ],
                ];
            });

        return Inertia::render('EventHistory', [
            'payments' => $payments,
        ]);
    }

    public function confirmStore(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'receipt' => 'required|file|mimes:jpg,png,jpeg,pdf|max:2048',
        ]);

        $payment = Payment::findOrFail($request->payment_id);
        $path = $request->file('receipt')->store('receipts', 'public');
        $payment->update(['receipt_path' => $path, 'paid_at' => now()]);

        // Jika ingin update status ticket juga, bisa tambahkan di sini

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['tickets.event']);

        return Inertia::render('PaymentDetail', [
            'payment' => [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'method' => $payment->method,
                'created_at' => $payment->created_at,
                'paid_at' => $payment->paid_at,
                'buyer_name' => $payment->buyer_name,
                'buyer_email' => $payment->buyer_email,
                'buyer_phone' => $payment->buyer_phone,
                'receipt_path' => $payment->receipt_path,
                'tickets' => $payment->tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'event_title' => $ticket->event->title ?? '-',
                        'participant_name' => $ticket->participant_name,
                        'participant_email' => $ticket->participant_email,
                        'participant_phone' => $ticket->participant_phone,
                        'price_paid' => $ticket->price_paid,
                        'status' => $ticket->status,
                        'created_at' => $ticket->created_at,
                        'qr_code_url' => route('tickets.qr-code', ['ticket' => $ticket->id]),
                    ];
                }),
            ],
        ]);
    }
}
