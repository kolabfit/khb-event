<?php

namespace App\Filament\Resources\TicketResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\TicketResource;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    // (opsional) override judul halaman:
    // protected static ?string $title = 'Detail Tiket';
}
