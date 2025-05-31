<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QrisSettingResource\Pages;
use App\Models\QrisSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class QrisSettingResource extends Resource
{
    protected static ?string $model = QrisSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Payment Management';
    protected static ?string $navigationLabel = 'QRIS Settings';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('merchant_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('merchant_city')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('qris_image_path')
                    ->image()
                    ->directory('qris')
                    ->visibility('public')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('500')
                    ->imageResizeTargetHeight('500'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('merchant_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merchant_city')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('qris_image_path')
                    ->square(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (QrisSetting $record) {
                        if ($record->qris_image_path) {
                            Storage::disk('public')->delete($record->qris_image_path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->qris_image_path) {
                                    Storage::disk('public')->delete($record->qris_image_path);
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQrisSettings::route('/'),
            'create' => Pages\CreateQrisSetting::route('/create'),
            'edit' => Pages\EditQrisSetting::route('/{record}/edit'),
        ];
    }
} 