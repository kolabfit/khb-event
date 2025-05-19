<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->button()
                ->extraAttributes(['class' => 'bg-khb-blue hover:bg-khb-blue/80']),
            Actions\DeleteAction::make()
                ->button()
                ->extraAttributes(['class' => 'bg-red-600 hover:bg-red-700']),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}