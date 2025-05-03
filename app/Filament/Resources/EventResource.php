<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers\TicketTypesRelationManager;
use App\Models\Event;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Resource;
use Filament\Tables\Table as FilamentTable;
use Filament\Tables;


class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon  = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?string $navigationLabel = 'Events';
    protected static ?int    $navigationSort  = 1;

    public static function form(FilamentForm  $form): FilamentForm
    {
        return $form
            ->schema([
                // Auto-assign user_id based on logged-in user
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),

                Forms\Components\TextInput::make('title')
                    ->label('Judul Event')
                    ->required()
                    ->maxLength(255)
                    ->extraAttributes([
                        'class' => 'bg-white border-khb-purple focus:border-khb-green',
                    ]),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug($state))),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->extraAttributes(['class' => 'bg-gray-50']),

                Forms\Components\TextInput::make('location')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('start_date')
                    ->label('Mulai Tanggal & Waktu')
                    ->required(),

                Forms\Components\DateTimePicker::make('end_date')
                    ->label('Selesai Tanggal & Waktu')
                    ->nullable(),

                Forms\Components\TextInput::make('quota')
                    ->label('Kuota Tiket')
                    ->required()
                    ->numeric(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft'    => 'Draft',
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),

                Forms\Components\MultiSelect::make('categories')
                    ->label('Kategori')
                    ->relationship('categories', 'name'),
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'px-4 py-2 text-sm']),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Organizer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'khb-purple' => 'draft',
                        'yellow'     => 'pending',
                        'green'      => 'approved',
                        'red'        => 'rejected',
                    ])
                    ->extraAttributes(['class' => 'uppercase text-xs']),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'    => 'Draft',
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
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
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Contoh Relation Manager
            // RelationManagers\TicketsRelationManager::class,
            TicketTypesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit'   => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
