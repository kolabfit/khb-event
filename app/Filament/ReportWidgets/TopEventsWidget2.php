<?php

namespace App\Filament\ReportWidgets;

use Filament\Widgets\Widget;
use App\Models\Event;

class TopEventsWidget2 extends Widget
{
    protected static string $view = 'filament.widgets.top-events-widget2';

    public function getViewData(): array
    {
        $events = Event::query()
            ->withCount('tickets')
            ->withSum('tickets as payments_sum_amount', 'price_paid')
            ->orderByDesc('tickets_count')
            ->limit(5)
            ->get();

        return [
            'events' => $events,
        ];
    }
}
