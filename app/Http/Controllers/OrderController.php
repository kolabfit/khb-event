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
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:qris',
        ]);

        $event = Event::findOrFail($data['id']);

        if ($data['quantity'] > $event->quota) {
            return back()
                ->withErrors(['quantity' => 'Kuota tidak mencukupi'])
                ->withInput();
        }

        $event->decrement('quota', $data['quantity']);

        $totalPaid = $event->price * $data['quantity'];

        $ticket = Ticket::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'price_paid' => $totalPaid,
            'status' => 'pending',
            'quantity' => $data['quantity'],
        ]);

        // Buat payment record untuk semua order, termasuk gratis
        $payment = Payment::create([
            'ticket_id' => $ticket->id,
            'amount' => $totalPaid,
            'method' => $totalPaid === 0 ? 'free' : $data['payment_method'],
            'status' => 'pending',
            'buyer_name' => $data['fullname'],
            'buyer_email' => $data['email'],
            'buyer_phone' => $data['phone'],
            'transaction_id' => Str::uuid(),
        ]);

        if ($totalPaid === 0) {

            $payment->update([
                'paid_at' => now(),
            ]);

            // Gratis: langsung ke dashboard
            return redirect()
                ->route('dashboard')
                ->with('success', 'Tiket gratis Anda berhasil dipesan, menunggu approval admin.');
        }

        // Berbayar: minta user upload bukti dulu
        return redirect()->route('payments.confirm', [
            'ticket' => $ticket->id,
        ]);
    }

    public function confirm(Ticket $ticket)
    {
        return Inertia::render('PaymentConfirmation', [
            'ticket' => [
                'id' => $ticket->id,
                'event_title' => $ticket->event->title,
                'quantity' => $ticket->quantity,
                'price_paid' => $ticket->price_paid,
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
