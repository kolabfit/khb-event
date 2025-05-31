<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventRequestResource\Pages;
use App\Models\EventRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class EventRequestResource extends Resource
{
    protected static ?string $model = EventRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    
    // protected static ?string $navigationGroup = 'Management';
    
    protected static ?string $navigationLabel = 'Event Requests';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('penanggungjawab')
                    ->required()
                    ->maxLength(255)
                    ->label('Penanggung Jawab'),
                Forms\Components\TextInput::make('kontak')
                    ->required()
                    ->maxLength(255)
                    ->label('Kontak'),
                Forms\Components\TextInput::make('alamat')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('namakegiatan')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Kegiatan'),
                Forms\Components\Textarea::make('deskripsi')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('namakegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('penanggungjawab')
                    ->label('Penanggung Jawab')
                    ->searchable(),
                TextColumn::make('kontak')
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),
                // BadgeColumn::make('status')
                //     ->colors([
                //         'warning' => 'pending',
                //         'success' => 'approved',
                //         'danger' => 'rejected',
                //     ]),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Submitted at'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventRequests::route('/'),
            'create' => Pages\CreateEventRequest::route('/create'),
            'view' => Pages\ViewEventRequest::route('/{record}'),
            'edit' => Pages\EditEventRequest::route('/{record}/edit'),
        ];
    }    
} 