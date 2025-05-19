<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Filament\Widgets\ApprovalTicketsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
            Actions\CreateAction::make()
                ->label('Create Tickets')
                ->icon('heroicon-o-plus'),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            ApprovalTicketsWidget::class,
        ];
    }
}
