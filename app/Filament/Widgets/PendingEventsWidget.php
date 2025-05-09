<?php
// app/Filament/Widgets/PendingEventsWidget.php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PendingEventsWidget extends TableWidget
{
    protected static ?string $heading = 'Pending Events';
    protected static ?string $pollingInterval = '60s';

    protected string|int|array $columnSpan = [
        'sm' => 2,
        'lg' => 'full',
    ];

    protected function getTableQuery(): Builder
    {
        return Event::query()
            ->where('status', 'pending')
            ->with('user');
    }

    protected function getTableColumns(): array
    {
        return [
            ImageColumn::make('thumbnail')
                ->label('Gambar')
                ->disk('public')
                ->size(50)
                ->url(fn ($record) => Storage::disk('public')->url($record->thumbnail)),

            TextColumn::make('title')
                ->label('Judul')
                ->sortable()
                ->searchable(),
            TextColumn::make('user.name')
                ->label('Organizer')
                ->sortable(),
            TextColumn::make('start_date')
                ->label('Tanggal Mulai')
                ->date(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-s-check')
                ->requiresConfirmation()
                ->action(fn(Event $record) => $record->update(['status' => 'approved']))
                ->color('success'),

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-s-x-mark')
                ->requiresConfirmation()
                ->action(fn(Event $record) => $record->update(['status' => 'rejected']))
                ->color('danger'),
        ];
    }
}
