<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables;
use App\Models\Event;
use Filament\Tables\Columns\ImageColumn;

class TopEventsWidget extends TableWidget
{
    protected static ?string $heading = 'Top 5 Events • Tickets & Revenue';

    protected string|int|array $columnSpan = 'full';
    

    /**
     * Tampilkan 5 event teratas.
     */
    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Event::query()
            // Hitung jumlah tiket terjual per event
            ->withCount('tickets')
            // Hitung total pendapatan per event via hasManyThrough
            ->withSum('payments', 'amount')
            ->orderByDesc('tickets_count')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            ImageColumn::make('thumbnail')
                ->label('Gambar')
                ->size(50),      
                
            Tables\Columns\TextColumn::make('title')
                ->label('Event')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('tickets_count')
                ->label('Tickets Sold')
                ->sortable(),

            Tables\Columns\TextColumn::make('payments_sum_amount')
                ->label('Revenue')
                ->money('idr', true)
                ->sortable(),
        ];
    }
}
