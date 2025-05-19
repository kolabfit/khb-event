<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class TicketScannerWidget extends Widget
{
    protected static string $view = 'filament.widgets.ticket-scanner-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Ticket Scanner';
} 