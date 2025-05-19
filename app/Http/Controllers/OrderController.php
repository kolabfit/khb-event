<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;


class OrderController extends Controller
{
    // Tampilkan halaman OrderPage
    public function create(Request $request)
    {
        // Baca query param id (atau event jika kamu gunakan key 'event')
        $eventId = $request->query('id') ?? $request->query('event');
        $event = Event::findOrFail($eventId);

        return Inertia::render('OrderPage', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
                'location' => $event->location,
                // field thumbnail_url atau yang sesuai di model Event
                'thumbnail_url' => $event->thumbnail,
                'price' => $event->price,
                'quota' => $event->quota,
                'price_label' => $event->price_label,
            ],
        ]);
    }

    // Proses penyimpanan order
    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:qris',
            'participants' => 'required|array|min:1',
            'participants.*.fullname' => 'required|string|max:255',
            'participants.*.email' => 'required|email|max:255',
            'participants.*.phone' => 'required|string|max:20',
        ]);

        $event = Event::findOrFail($data['id']);

        if ($data['quantity'] > $event->quota) {
            return back()
                ->withErrors(['quantity' => 'Kuota tidak mencukupi'])
                ->withInput();
        }

        \DB::transaction(function () use ($event, $data, &$payment) {
            $event->decrement('quota', $data['quantity']);

            $totalPaid = $event->price * $data['quantity'];
            $transactionId = Str::uuid()->toString();

            // Buat 1 Payment
            $payment = Payment::create([
                'amount' => $totalPaid,
                'method' => $totalPaid === 0 ? 'Gratis' : $data['payment_method'],
                'status' => 'pending',
                'buyer_name' => auth()->user()->name,
                'buyer_email' => auth()->user()->email,
                'buyer_phone' => auth()->user()->phone ?? '',
                'transaction_id' => $transactionId,
            ]);

            // Loop tiap peserta, buat Ticket-nya
            foreach ($data['participants'] as $participant) {
                Ticket::create([
                    'event_id' => $event->id,
                    'user_id' => auth()->id(),
                    'payment_id' => $payment->id,
                    'price_paid' => $event->price ?? 0,
                    'status' => 'pending',
                    'participant_name' => $participant['fullname'],
                    'participant_email' => $participant['email'],
                    'participant_phone' => $participant['phone'],
                ]);
            }

            if ($totalPaid === 0) {
                $payment->update([
                    'paid_at' => now(),
                    'status' => 'paid',
                ]);
                $payment->tickets()->update([
                    'status' => 'paid',
                ]);
            }
        });

        $firstTicket = $payment->tickets()->first();

        // Redirect ke konfirmasi atau dashboard
        if ($payment->amount === 0) {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Tiket gratis berhasil dipesan, menunggu approval admin.');
        }

        // Bisa kirim ke halaman konfirmasi pembayaran, misal:
        return redirect()->route('payments.confirm', [
            'ticket' => $firstTicket->id,
        ]);
    }

    public function confirm(Ticket $ticket)
    {
        // Hitung berapa peserta di transaksi ini
        $quantity = $ticket->payment->tickets()->count();

        // Ambil total dari Payment (atau: $ticket->price_paid * $quantity)
        $totalPaid = $ticket->payment->amount;

        return Inertia::render('PaymentConfirmation', [
            'ticket' => [
                'id' => $ticket->id,
                'event_title' => $ticket->event->title,
                'quantity' => $quantity,
                'price_paid' => $ticket->price_paid,
                'total_paid' => $ticket->payment->amount,     
                'price_per_ticket' => $ticket->price_paid,         // harga satu tiket
            ],
        ]);
    }

    public function confirmStore(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'receipt' => 'required|file|mimes:jpg,png,jpeg,pdf|max:2048',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);
        $path = $request->file('receipt')->store('receipts', 'public');

        // Simpan path bukti ke payment (asumsi relasi ticket->payment)
        $ticket->payment->update(['receipt_path' => $path, 'paid_at' => now()]);

        // Tandai ticket paid
        // $ticket->update(['status' => 'paid']);

        return redirect()->route('dashboard')->with('success', 'Bukti pembayaran berhasil diunggah.');
    }
}
