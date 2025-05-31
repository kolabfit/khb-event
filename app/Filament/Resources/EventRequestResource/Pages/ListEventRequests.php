<?php

namespace App\Filament\Resources\EventRequestResource\Pages;

use App\Filament\Resources\EventRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventRequests extends ListRecords
{
    protected static string $resource = EventRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
} 