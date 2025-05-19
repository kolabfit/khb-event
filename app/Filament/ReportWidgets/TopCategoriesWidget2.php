<?php

namespace App\Filament\ReportWidgets;

use Filament\Widgets\Widget;
use App\Models\Category;

class TopCategoriesWidget2 extends Widget
{
    protected static string $view = 'filament.widgets.top-categories-widget2';

    public function getViewData(): array
    {
        $categories = Category::withCount(['events as paid_transactions_count' => function ($q) {
            $q->whereHas('tickets.payment', fn($q) => $q->where('status', 'paid'));
        }])
        ->orderByDesc('paid_transactions_count')
        ->limit(5)
        ->get();

        return [
            'categories' => $categories,
        ];
    }
} 