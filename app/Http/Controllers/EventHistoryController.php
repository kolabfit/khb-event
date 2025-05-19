<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use Inertia\Inertia;

class EventHistoryController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('event')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $events = $tickets->map(function ($ticket) {
            return [
                'id' => $ticket->event->id,
                'title' => $ticket->event->title,
                'start_date' => $ticket->event->start_date,
                'end_date' => $ticket->event->end_date,
                'location' => $ticket->event->location,
                'thumbnail_url' => $ticket->event->thumbnail,
                'price' => $ticket->event->price,
                'quantity' => $ticket->quantity,
                'status' => $ticket->status,
            ];
        });

        return Inertia::render('EventHistory', [
            'events' => $events,
        ]);
    }
}
