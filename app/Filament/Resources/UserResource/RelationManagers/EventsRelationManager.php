<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';
    protected static ?string $recordTitleAttribute = 'title';

    public static function shouldCheckAccess(): bool
    {
        return true;
    }

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->hasRole('admin');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('event_status')
                    ->label('Event Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ongoing' => 'success',
                        'upcoming' => 'info',
                        'ended' => 'danger',
                        default => 'gray',
                    })
                    ->state(function ($record): string {
                        $now = now();
                        if ($record->start_date && $record->end_date) {
                            if ($now->between($record->start_date, $record->end_date)) {
                                return 'ongoing';
                            }
                            if ($record->start_date > $now) {
                                return 'upcoming';
                            }
                            return 'ended';
                        }
                        if ($record->start_date && $record->start_date > $now) {
                            return 'upcoming';
                        }
                        if ($record->end_date && $record->end_date < $now) {
                            return 'ended';
                        }
                        return 'ongoing';
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'ended' => 'Ended',
                    ]),
                Tables\Filters\SelectFilter::make('event_status')
                    ->options([
                        'ongoing' => 'Ongoing',
                        'upcoming' => 'Upcoming',
                        'ended' => 'Ended',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->button()
                    ->extraAttributes(['class' => 'bg-khb-blue hover:bg-khb-blue/80']),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->extraAttributes(['class' => 'bg-khb-green hover:bg-khb-green/80']),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->extraAttributes(['class' => 'bg-red-600 hover:bg-red-700']),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        // Ambil langsung query builder dari relasi 'events'
        return $this->getOwnerRecord()
            ->events()
            ->getQuery()
            ->orderByRaw("
                CASE
                    WHEN NOW() BETWEEN start_date AND end_date THEN 0  -- ongoing
                    WHEN start_date > NOW() THEN 1                    -- upcoming
                    ELSE 2                                           -- ended
                END ASC
            ")
            ->orderByRaw("
                CASE
                    WHEN NOW() BETWEEN start_date AND end_date THEN end_date
                    WHEN start_date > NOW() THEN start_date
                    ELSE end_date
                END ASC
            ");
    }
}
