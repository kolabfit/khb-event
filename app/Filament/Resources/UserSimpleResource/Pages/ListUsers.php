<?php

namespace App\Filament\Resources\UserSimpleResource\Pages;

use App\Filament\Resources\UserSimpleResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListUsers extends ListRecords
{
    protected static string $resource = UserSimpleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 