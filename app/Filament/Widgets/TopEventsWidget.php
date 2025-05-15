<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class TopEventsWidget extends TableWidget
{
    /**
     * Judul widget pada dashboard
     */
    protected static ?string $heading = 'Top Selling Events';

    /**
     * Lebar kolom widget
     */
    protected string|int|array $columnSpan = 'full';

    /**
     * Batas jumlah event yang ditampilkan
     */
    protected int $limit = 5;

    /**
     * Query untuk mengambil data top selling events
     *
     * @return Builder|Relation|null
     */
    protected function getTableQuery(): Builder|Relation|null
    {
        return Event::query()
            ->withCount('tickets')
            ->withSum('payments', 'amount')
            ->orderByDesc('tickets_count')
            ->limit($this->limit);
    }

    /**
     * Definisi kolom tabel widget
     *
     * @return array<int, Tables\Columns\Column>
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('thumbnail')
                ->label('Thumbnail')
                ->url(fn(Event $record): string => asset($record->thumbnail))
                // ->getStateUsing(fn(Event $record) => $record->thumbnail)
                ->width(120)
                ->height(80),

            TextColumn::make('title')
                ->label('Event')
                ->searchable()
                ->sortable(),

            TextColumn::make('tickets_count')
                ->label('Tickets Sold')
                ->sortable(),

            TextColumn::make('payments_sum_amount')
                ->label('Revenue')
                ->formatStateUsing(
                    fn($state): string =>
                    'Rp ' . number_format($state ?? 0, 0, ',', '.')
                )
                ->sortable(),
        ];
    }
}
