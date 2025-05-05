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
    // Tampilkan 4 kartu per baris
    protected static int|array $columns = 4;

    protected function getCards(): array
    {
        $totalEvents  = Event::count();
        $totalUsers   = User::count();
        $ticketsSold  = Ticket::where('status', 'paid')->count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');

        // Format Rupiah
        $formattedRevenue = 'Rp ' . number_format($totalRevenue, 0, ',', '.');

        return [
            Card::make('Total Events', $totalEvents)
                ->color('secondary'),

            Card::make('Total Users', $totalUsers)
                ->color('primary'),

            Card::make('Tickets Sold', $ticketsSold)
                ->color('success'),

            Card::make('Total Revenue', $formattedRevenue)
                ->color('danger'),
        ];
    }
}
