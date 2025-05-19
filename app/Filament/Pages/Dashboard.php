<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;


class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';

    // protected static ?array $widgets = [
    //    TicketScannerWidget::class,
    //    StatsOverview::class,
    //    TicketSalesChart::class,
    //    TopEventsWidget::class,

    // ];
}
