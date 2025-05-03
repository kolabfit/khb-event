<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\TableWidget;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class PendingItemsTable extends TableWidget
{
    // 1) Ubah jadi static
    protected static ?string $heading = 'Pending Approval';

    // full width
    protected string|int|array $columnSpan = '2';

    // single row height
    protected int|array $rowSpan = 1;

    // 2) Tambahkan return type
    protected function getTableQuery(): Builder|Relation|null
    {
        return Event::query()
            ->with('user')
            ->where('status', 'pending')
            ->orderBy('start_date', 'asc');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')
                ->label('Judul')
                ->searchable()
                ->sortable(),

            TextColumn::make('user.name')
                ->label('EO')
                ->searchable()
                ->sortable(),

            TextColumn::make('start_date')
                ->label('Tanggal Mulai')
                ->dateTime('d M Y H:i')
                ->sortable(),

            BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'approved',
                    'danger'  => 'rejected',
                ])
                // opsional: ubah teks “pending” → “Pending”
                ->formatStateUsing(fn (string $state): string => ucfirst($state)),

            TextColumn::make('created_at')
                ->label('Dibuat')
                ->dateTime('d M Y')
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('approve')
                ->label('Approve')
                ->action(fn (Event $record) => $record->update(['status' => 'approved']))
                ->requiresConfirmation()
                ->icon('heroicon-o-check-circle'),

            Tables\Actions\Action::make('reject')
                ->label('Reject')
                ->action(fn (Event $record) => $record->update(['status' => 'rejected']))
                ->requiresConfirmation()
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
