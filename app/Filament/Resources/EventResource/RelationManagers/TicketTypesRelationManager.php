<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form as FilamentForm;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table as FilamentTable;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;

class TicketTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketTypes';
    protected static ?string $recordTitleAttribute = 'name';

    // 1) Form schema untuk Create/Edit TicketType
    public function form(FilamentForm $form): FilamentForm
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Tipe Tiket')
                    ->required()
                    ->maxLength(255),

                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->required(),

                TextInput::make('quota')
                    ->label('Kuota Total')
                    ->numeric()
                    ->required(),
            ]);
    }

    // 2) Tabel untuk listing TicketType
    public function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->headerActions([
                CreateAction::make()->label('Tambah Tipe Tiket'),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn($state, $record): string => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('quota')
                    ->label('Kuota Total')
                    ->sortable(),

                TextColumn::make('remaining_quota')
                    ->label('Kuota Tersisa')
                    ->sortable(),
                TextColumn::make('paid_tickets_count')
                    ->label('Tiket Paid')
                    ->getStateUsing(fn($record) => $record->tickets()->where('status', 'paid')->count()),
                TextColumn::make('used_tickets_count')
                    ->label('Tiket Used')
                    ->getStateUsing(fn($record) => $record->tickets()->where('status', 'used')->count()),
            ])
            ->actions([
                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
