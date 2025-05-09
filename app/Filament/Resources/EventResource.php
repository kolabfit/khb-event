<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table as FilamentTable;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?string $navigationLabel = 'Events';
    protected static ?int $navigationSort = 1;

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(fn() => auth()->id()),

                TextInput::make('title')
                    ->label('Judul Event')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->label('Kata Kunci')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Str::slug($state))),

                Forms\Components\FileUpload::make('thumbnail')
                    ->label('Thumbnail')
                    ->image()
                    ->imagePreviewHeight('200')
                    ->required(),

                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'link',
                        'bulletList',
                        'numberList',
                        'blockquote',
                        'codeBlock',
                        'attachFiles',
                    ])
                    ->extraAttributes([
                        'style' => 'min-height:250px;',
                    ]),

                TextInput::make('location')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('start_date')
                    ->label('Acara Mulai')
                    ->required(),

                Forms\Components\DateTimePicker::make('end_date')
                    ->label('Acara Selesai')
                    ->nullable(),

                TextInput::make('quota')
                    ->label('Kuota Tiket')
                    ->required()
                    ->numeric(),

                TextInput::make('price')
                    ->label('Harga Tiket')
                    ->required()
                    // 1) Mask frontend: tambahkan “Rp ” di depan
                    ->mask(
                        RawJs::make("
                            (value) => {
                                const digits = value.replace(/\\D/g, '');
                                const formatted = digits.replace(/\\B(?=(\\d{3})+(?!\\d))/g, '.');
                                return formatted ? `Rp \${formatted}` : '';
                            }
                        ")
                    )

                    // 2) Strip prefix + titik sebelum simpan
                    ->dehydrateStateUsing(
                        fn(?string $state) => $state === null
                        ? null
                        : (int) str_replace(['Rp ', '.'], '', $state)
                    )
                    // 3) Saat load form (edit), tampilkan ulang dengan prefix
                    ->afterStateHydrated(
                        fn(?int $state) => $state === null
                        ? ''
                        : 'Rp ' . number_format($state, 0, ',', '.')
                    ),

                Forms\Components\MultiSelect::make('categories')
                    ->label('Kategori')
                    ->relationship('categories', 'name')
                    ->helperText('Pilih kategori yang sudah ditambahkan sebelumnya.'),
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->size(50),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Organizer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->extraAttributes([
                        'class' => 'bg-khb-green hover:bg-khb-green/80',
                    ]),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->extraAttributes([
                        'class' => 'bg-red-600 hover:bg-red-700',
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
