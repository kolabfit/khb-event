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
use Filament\Forms\Components\Select;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table as FilamentTable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\CreateAction;  // pastikan ini di-import
use Filament\Tables\Columns\BadgeColumn;
use App\Filament\Resources\EventResource\RelationManagers;

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
                    ->reactive(),

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

                Select::make('qris_setting_id')
                    ->label('Pilihan QRIS')
                    ->relationship('qrisSetting', 'merchant_name')
                    ->nullable()
                    ->placeholder('Pilih Pengaturan QRIS')
                    ->visible(fn($get) => $get('is_paid'))
                    ->required(fn($get) => $get('is_paid')),
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
                    ->sortable()
                    ->url(fn($record) => route('filament.admin.resources.events.view', $record))
                    ->openUrlInNewTab(false),

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

                Tables\Columns\BadgeColumn::make('status_label')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'danger' => 'ended',
                        'warning' => 'draft',
                        'success' => 'active',
                        'info' => 'upcoming',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->button()
                    ->extraAttributes(['class' => 'bg-blue-500 hover:bg-blue-600']),
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
            'view' => Pages\ViewEvent::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->orderByRaw("
                CASE 
                    WHEN end_date >= NOW() OR (end_date IS NULL AND start_date >= NOW()) THEN 0
                    ELSE 1
                END ASC
            ")
            ->orderByRaw("
                CASE 
                    WHEN end_date >= NOW() OR (end_date IS NULL AND start_date >= NOW()) THEN 
                        COALESCE(start_date, end_date)
                    ELSE NULL
                END ASC
            ")
            ->orderBy('end_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TicketsRelationManager::class,
        ];
    }
}
