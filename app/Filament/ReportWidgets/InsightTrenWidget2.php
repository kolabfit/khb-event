<?php

namespace App\Filament\ReportWidgets;

use App\Models\Payment;
use Filament\Widgets\Widget;

class InsightTrenWidget2 extends Widget
{
    protected static string $view = 'filament.widgets.insight-tren-widget';

    public function getViewData(): array
    {
        $now = now();
        $thisWeek = Payment::whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()])->where('status', 'paid');
        $lastWeek = Payment::whereBetween('created_at', [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()])->where('status', 'paid');

        $thisWeekCount = $thisWeek->count();
        $lastWeekCount = $lastWeek->count();
        $thisWeekRevenue = $thisWeek->sum('amount');
        $lastWeekRevenue = $lastWeek->sum('amount');

        $countChange = $lastWeekCount > 0 ? round((($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100, 1) : 0;
        $revenueChange = $lastWeekRevenue > 0 ? round((($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100, 1) : 0;

        return [
            'thisWeekCount' => $thisWeekCount,
            'lastWeekCount' => $lastWeekCount,
            'countChange' => $countChange,
            'thisWeekRevenue' => $thisWeekRevenue,
            'lastWeekRevenue' => $lastWeekRevenue,
            'revenueChange' => $revenueChange,
        ];
    }
} 