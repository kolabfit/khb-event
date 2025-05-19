<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Inertia\Inertia;

class PaymentHistoryController extends Controller
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
} 