<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TicketSalesChart;
use App\Filament\Widgets\PendingItemsTable;
use Filament\Widgets\Widget; 
use App\Filament\Widgets\TicketSalesWidget;
use App\Filament\Widgets\TopEventsWidget; // ← import
use App\Filament\Widgets\PendingEventsWidget; // ← import



class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';

    // override header widgets → kosongkan
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    // main widgets yang mau tampil
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            TicketSalesChart::class,
            // PendingItemsTable::class,
            // TicketSalesWidget::class,
            TopEventsWidget::class,
            PendingEventsWidget::class,
        ];
    }

    // override footer widgets → kosongkan
    protected function getFooterWidgets(): array
    {
        return [];
    }
}
