<?php

namespace App\Filament\ReportWidgets;

use Filament\Widgets\Widget;
use App\Models\Payment;

class TransactionsPerWeekChart2 extends Widget
{
    protected static string $view = 'filament.widgets.transactions-per-week-chart2';

    public function getViewData(): array
    {
        $now = now();
        $start = $now->copy()->subWeeks(7)->startOfWeek();
        $weeks = collect(range(0, 7))->map(fn($i) => $start->copy()->addWeeks($i));

        $labels = $weeks->map(fn($date) => $date->format('d M'))->all();
        $data = $weeks->map(function ($date) {
            return Payment::whereBetween('created_at', [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()])
                ->where('status', 'paid')
                ->count();
        })->toArray();

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
} 