<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Event;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Payment;

class StatsOverview extends StatsOverviewWidget
{

    protected function getCards(): array
    {
        // Hitung event berdasarkan status
        $draftCount     = Event::where('status', 'draft')->count();
        $pendingCount   = Event::where('status', 'pending')->count();
        $approvedCount  = Event::where('status', 'approved')->count();
        $rejectedCount  = Event::where('status', 'rejected')->count();

        // Statistik pengguna (EO & User)
        $totalUsers     = User::count();

        // Statistik tiket
        $ticketsSold    = Ticket::where('status', 'paid')->count();

        // Pendapatan
        $totalRevenue   = Payment::where('status', 'paid')->sum('amount');
        $formattedRev   = number_format($totalRevenue, 0, ',', '.');

        // Event terlaris (berdasarkan jumlah tickets)
        $topEvent = Event::withCount('tickets')
            ->orderBy('tickets_count', 'desc')
            ->first();
        $topCount = $topEvent?->tickets_count ?? 0;
        $topTitle = $topEvent?->title        ?? '-';

        return [
            // Breakdown status event
            Card::make('Draft',   $draftCount)
                ->color('secondary'),
            Card::make('Pending', $pendingCount)
                ->color('warning'),
            Card::make('Approved',$approvedCount)
                ->color('success'),
            Card::make('Rejected',$rejectedCount)
                ->color('danger'),

            // Total users
            Card::make('Total Users', $totalUsers),

            // Total tickets sold
            Card::make('Tickets Sold', $ticketsSold),

            // Total revenue
            Card::make('Total Revenue', "Rp {$formattedRev}"),

            // Event terlaris
            Card::make('Top Event', $topCount)
                ->description($topTitle)
                ->color('primary'),
        ];
    }
}
