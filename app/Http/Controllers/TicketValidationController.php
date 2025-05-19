<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class TicketValidationController extends Controller
{
    public function showValidationPage()
    {
        return view('admin.ticket-validation');
    }

    public function validateTicket(Request $request)
    {
        $ticketId = $request->ticket_id;
        $ticket = Ticket::with(['event', 'payment'])->find($ticketId);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket tidak ditemukan',
                'ticket' => null
            ]);
        }

        // Helper untuk detail tiket
        $ticketDetail = [
            'id' => $ticket->id,
            'event' => $ticket->event->title ?? '-',
            'participant' => $ticket->participant_name,
            'status' => $ticket->status,
            'validated_at' => now()->format('d M Y H:i')
        ];

        // Cek status tiket
        if ($ticket->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket sudah dibatalkan',
                'ticket' => $ticketDetail
            ]);
        }

        if ($ticket->status === 'used') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket sudah digunakan',
                'ticket' => $ticketDetail
            ]);
        }

        // Cek pembayaran
        if ($ticket->payment && $ticket->payment->status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran belum dikonfirmasi',
                'ticket' => $ticketDetail
            ]);
        }

        // Update status tiket menjadi used
        $ticket->update(['status' => 'used']);

        // Update detail status
        $ticketDetail['status'] = 'used';

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil divalidasi',
            'ticket' => $ticketDetail
        ]);
    }
} 