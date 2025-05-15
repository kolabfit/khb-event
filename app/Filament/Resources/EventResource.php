<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table as FilamentTable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\CreateAction;  // pastikan ini di-import

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

                FileUpload::make('thumbnail')
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
                    ->extraAttributes(['style' => 'min-height:250px;']),

                TextInput::make('location')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(255),

                DateTimePicker::make('start_date')
                    ->label('Acara Mulai')
                    ->required(),

                DateTimePicker::make('end_date')
                    ->label('Acara Selesai')
                    ->nullable(),

                TextInput::make('quota')
                    ->label('Kuota Tiket')
                    ->required()
                    ->numeric(),

                Toggle::make('is_paid')
                    ->label('Event Berbayar?')
                    ->default(true)
                    ->reactive()  // <-- buat biar field lain bisa merespon perubahan secara langsung
                    ->disabled(fn(bool $state): bool => !$state),

                TextInput::make('price')
                    ->label('Harga Tiket (IDR)')
                    ->reactive()
                    ->visible(fn($get) => $get('is_paid'))
                    ->required(fn($get) => $get('is_paid'))
                    // Mask: setiap input akan di-strip non-digit, lalu diformat pakai Intl.NumberFormat
                    ->mask(
                        RawJs::make(<<<'JS'
            (value) => {
                // Buang semua karakter kecuali digit
                const digits = value.replace(/\D/g, '');
                if (!digits) {
                    return '';
                }
                // Format ribuan sesuai locale id-ID
                const formatted = new Intl.NumberFormat('id-ID').format(digits);
                return `Rp ${formatted}`;
            }
        JS)
                    )
                    // Simpan state sebagai integer tanpa prefix/titik
                    ->dehydrateStateUsing(
                        fn(?string $state) =>
                        $state
                        ? (int) str_replace(['Rp ', '.'], '', $state)
                        : null
                    )
                    // Ketika form di-load (edit), tampilkan dalam format rupiah
                    ->afterStateHydrated(
                        fn(?int $state) =>
                        $state !== null
                        ? 'Rp ' . number_format($state, 0, ',', '.')
                        : ''
                    ),

                MultiSelect::make('categories')
                    ->label('Kategori')
                    ->helperText('Pilih kategori yang sudah ditambahkan sebelumnya.')
                    ->relationship('categories', 'name'),
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->url(fn(Event $record): string => asset($record->thumbnail))
                    // ->getStateUsing(fn(Event $record) => $record->thumbnail)
                    ->width(120)
                    ->height(80),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                // Tables\Columns\IconColumn::make('is_paid')
                //     ->label('Berbayar')
                //     ->boolean(),

                TextColumn::make('formatted_price')
                    ->label('Harga')
                    // Ambil langsung dari model→price
                    ->getStateUsing(
                        fn(Event $record) => ((int) $record->price) > 0
                        ? 'Rp ' . number_format($record->price, 0, ',', '.')
                        : 'Gratis'
                    )
                    // tetap sortable berdasarkan kolom price di DB
                    ->sortable('price'),

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
                    ->extraAttributes(['class' => 'bg-khb-green hover:bg-khb-green/80']),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->extraAttributes(['class' => 'bg-red-600 hover:bg-red-700']),
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
