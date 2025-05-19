<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';
    protected static ?string $title = 'Daftar Tiket';
    protected static ?string $recordTitleAttribute = 'id';

    public function table(Tables\Table $table): Tables\Table
    {
        $total = $this->getOwnerRecord()
            ->tickets()
            ->whereIn('tickets.status', ['paid', 'used'])
            ->join('events', 'tickets.event_id', '=', 'events.id')
            ->sum('events.price');

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Tiket')
                    ->sortable(),
                TextColumn::make('participant_name')
                    ->label('Nama Peserta')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('user.phone')
                    ->label('No. HP'),
                TextColumn::make('event.price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Beli')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'success' => 'paid',
                        'gray' => 'used',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->contentFooter(
                fn () => view('vendor.filament.components.tickets-total', [
                    'total' => number_format($total ?? 0, 0, ',', '.'),
                    'label' => 'Total Pendapatan'
                ])
            );
    }

    public function getTableQuery(): Builder
    {
        return $this->getOwnerRecord()
            ->tickets()
            ->getQuery()
            ->whereIn('tickets.status', ['paid', 'used']);
    }
} 