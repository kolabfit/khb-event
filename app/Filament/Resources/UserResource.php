<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\RelationManagers\EventsRelationManager;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Password;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Resource;
use Filament\Tables\Table as FilamentTable;
use Filament\Tables;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ToggleColumn;



class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationLabel = 'Users';
    protected static ?int $navigationSort = 2;

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->extraAttributes(['class' => 'bg-white']),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->extraAttributes(['class' => 'bg-white']),

                TextInput::make('password')->password()
                    ->label('Password')
                    ->minLength(8)
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->extraAttributes(['class' => 'bg-white']),

                MultiSelect::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->required()
                    ->extraAttributes(['class' => 'bg-gray-50']),
                // Toggle Active untuk Edit page
                Toggle::make('is_active')
                    ->label('Active')
                    // hanya pada Edit, hide di Create
                    ->visible(fn ($livewire) => $livewire instanceof Pages\EditUser),
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'px-4 py-2']),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->extraAttributes(['class' => 'px-4 py-2']),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->sortable()
                    ->wrap()
                    ->extraAttributes(['class' => 'px-4 py-2']),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->extraAttributes(['class' => 'px-4 py-2']),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->relationship('roles', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->button()
                    ->extraAttributes(['class' => 'bg-khb-blue hover:bg-khb-blue/80']),
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
            EventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
