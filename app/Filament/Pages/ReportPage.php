<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\Action;            // untuk Action::make()
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use App\Models\Payment;
use App\Models\Category;
use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Card;
use Filament\Support\Facades\FilamentView;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Table;
use App\Filament\Resources\PaymentResource;
use App\Filament\ReportWidgets\InsightTrenWidget2;
use App\Filament\ReportWidgets\TransactionsPerWeekChart2;
use App\Filament\ReportWidgets\TopCategoriesWidget2;
use App\Filament\ReportWidgets\TopEventsWidget2;
use App\Filament\ReportWidgets\WidgetTest;


class ReportPage extends Page implements HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $view = 'filament.pages.report-page';

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationGroup = 'Payment Management';

    // Add properties to store filter values
    public $timeFilter = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $eventFilter = '';
    public $dateFromFilter = null;
    public $dateUntilFilter = null;
    public $eventOrganizerFilter = null;

    // protected static ?array $widgets = [
    //     WidgetTest::class,
    //     // \App\Filament\ReportWidgets\TransactionsPerWeekChart2::class,
    //     // \App\Filament\ReportWidgets\TopCategoriesWidget2::class,
    //     // \App\Filament\ReportWidgets\InsightTrenWidget2::class,
    //     // \App\Filament\ReportWidgets\TopEventsWidget2::class,
    // ];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getViewData(): array
    {
        $query = Payment::query()->where('status', 'paid');

        // Apply time period filter
        if (!empty($this->timeFilter)) {
            $query = match ($this->timeFilter) {
                'today' => $query->whereDate('created_at', today()),
                'yesterday' => $query->whereDate('created_at', today()->subDay()),
                'this_week' => $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]),
                'last_week' => $query->whereBetween('created_at', [
                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek()
                ]),
                'this_month' => $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
                'last_month' => $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year),
                'this_year' => $query->whereYear('created_at', now()->year),
                default => $query,
            };
        }

        // Apply category filter
        if (!empty($this->categoryFilter)) {
            $query->whereHas('ticket.event.categories', fn($q) => $q->where('id', $this->categoryFilter));
        }

        // Apply event filter
        if (!empty($this->eventFilter)) {
            $query->whereHas('ticket.event', fn($q) => $q->where('title', 'like', '%' . $this->eventFilter . '%'));
        }

        // Get current table filter states
        $tableFilters = $this->tableFilters;

        // Apply date range filter from table filters
        if (!empty($tableFilters['date_range']['created_from'])) {
            $query->whereDate('created_at', '>=', $tableFilters['date_range']['created_from']);
        }

        if (!empty($tableFilters['date_range']['created_until'])) {
            $query->whereDate('created_at', '<=', $tableFilters['date_range']['created_until']);
        }

        // Apply event organizer filter from table filters
        if (!empty($tableFilters['eo_id']['eo_id'])) {
            $query->whereHas('ticket.event', fn($q) => $q->where('user_id', $tableFilters['eo_id']['eo_id']));
        }

        return [
            'totalRevenue' => $query->sum('amount'),
            'totalTransactions' => $query->count(),
            'totalEvents' => Event::count(),
            'totalUsers' => User::count(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Laporan')
                    ->description('Gunakan filter di bawah untuk menyesuaikan data laporan.')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('timeFilter')
                                    ->label('Periode Waktu')
                                    ->placeholder('Pilih Periode')
                                    ->options([
                                        '' => 'Semua Waktu',
                                        'today' => 'Hari Ini',
                                        'yesterday' => 'Kemarin',
                                        'this_week' => 'Minggu Ini',
                                        'last_week' => 'Minggu Lalu',
                                        'this_month' => 'Bulan Ini',
                                        'last_month' => 'Bulan Lalu',
                                        'this_year' => 'Tahun Ini',
                                    ])
                                    ->default('')
                                    ->live()
                                    ->afterStateUpdated(function () {
                                        $this->resetTableFiltersForm();
                                    }),
                                Select::make('statusFilter')
                                    ->label('Status')
                                    ->placeholder('Pilih Status')
                                    ->options([
                                        '' => 'Semua Status',
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                    ])
                                    ->default('')
                                    ->live()
                                    ->afterStateUpdated(function () {
                                        $this->resetTableFiltersForm();
                                    }),
                                Select::make('categoryFilter')
                                    ->label('Kategori')
                                    ->placeholder('Pilih Kategori')
                                    ->options(function () {
                                        $categories = \App\Models\Category::pluck('name', 'id')->toArray();
                                        return ['' => 'Semua Kategori'] + $categories;
                                    })
                                    ->default('')
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function () {
                                        $this->resetTableFiltersForm();
                                    }),
                                Select::make('eventFilter')
                                    ->label('Event')
                                    ->placeholder('Pilih Event')
                                    ->options(function () {
                                        $events = \App\Models\Event::pluck('title', 'id')->toArray();
                                        return ['' => 'Semua Event'] + $events;
                                    })
                                    ->default('')
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function () {
                                        $this->resetTableFiltersForm();
                                    }),
                            ])->columns(2),
                    ])
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Cari event, user, atau EO...')
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->headerActions($this->getTableHeaderActions())
            ->searchable()
            ->persistSearchInSession()
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Tidak ada data ditemukan')
            ->emptyStateDescription('Coba ubah filter atau kata kunci pencarian Anda.')
            ->emptyStateIcon('heroicon-o-document-magnifying-glass')
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => PaymentResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('ticket.id')
                    ->label('Ticket ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket.event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ticket.event.user.name')
                    ->label('Pembuat Event')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket.user.name')
                    ->label('Pembeli')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('idr', true),
                Tables\Columns\TextColumn::make('method')
                    ->label('Method'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'paid',
                        'heroicon-o-x-circle' => 'failed',
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment.amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->sortable(),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        $query = Payment::query()
            ->with(['ticket.event.categories', 'ticket.user', 'ticket.event.user'])
            ->orderBy('created_at', 'desc');

        // Apply time period filter
        if (!empty($this->timeFilter)) {
            $query = match ($this->timeFilter) {
                'today' => $query->whereDate('created_at', today()),
                'yesterday' => $query->whereDate('created_at', today()->subDay()),
                'this_week' => $query->whereBetween('created_at', [
                    now()->startOfWeek(), 
                    now()->endOfWeek()
                ]),
                'last_week' => $query->whereBetween('created_at', [
                    now()->subWeek()->startOfWeek(), 
                    now()->subWeek()->endOfWeek()
                ]),
                'this_month' => $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
                'last_month' => $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year),
                'this_year' => $query->whereYear('created_at', now()->year),
                default => $query,
            };
        }
        
        // Apply status filter
        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }
        
        // Apply category filter
        if (!empty($this->categoryFilter)) {
            $query->whereHas('ticket.event.categories', fn($q) => $q->where('id', $this->categoryFilter));
        }

        // Apply event filter
        if (!empty($this->eventFilter)) {
            $query->whereHas('ticket.event', fn($q) => $q->where('id', $this->eventFilter));
        }

        // Apply date range filter from table filters
        $tableFilters = $this->tableFilters;
        if (!empty($tableFilters['date_range']['created_from'])) {
            $query->whereDate('created_at', '>=', $tableFilters['date_range']['created_from']);
        }
        
        if (!empty($tableFilters['date_range']['created_until'])) {
            $query->whereDate('created_at', '<=', $tableFilters['date_range']['created_until']);
        }
        
        // Apply event organizer filter from table filters
        if (!empty($tableFilters['eo_id']['eo_id'])) {
            $query->whereHas('ticket.event', fn($q) => $q->where('user_id', $tableFilters['eo_id']['eo_id']));
        }
        
        return $query;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('ticket.id')
                ->label('Ticket ID')
                ->searchable(),
            Tables\Columns\TextColumn::make('ticket.event.title')
                ->label('Event')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('ticket.event.user.name')
                ->label('Pembuat Event')
                ->searchable(),
            Tables\Columns\TextColumn::make('ticket.user.name')
                ->label('Pembeli')
                ->searchable(),
            Tables\Columns\TextColumn::make('amount')
                ->label('Amount')
                ->money('idr', true),
            Tables\Columns\TextColumn::make('method')
                ->label('Method'),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'paid',
                    'danger' => 'failed',
                ])
                ->icons([
                    'heroicon-o-clock' => 'pending',
                    'heroicon-o-check-circle' => 'paid',
                    'heroicon-o-x-circle' => 'failed',
                ])
                ->label('Status'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Date')
                ->dateTime('d M Y H:i')
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('date_range')
                ->label('Tanggal')
                ->form([
                    DatePicker::make('created_from')->label('Dari'),
                    DatePicker::make('created_until')->label('Sampai'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'] ?? null,
                            fn($q, $d) => $q->whereDate('created_at', '>=', $d),
                        )
                        ->when(
                            $data['created_until'] ?? null,
                            fn($q, $d) => $q->whereDate('created_at', '<=', $d),
                        );
                }),

            // Event Organizer
            Filter::make('eo_id')
                ->label('Event Organizer')
                ->form([
                    Select::make('eo_id')
                        ->label('Pilih EO')
                        ->options(\App\Models\User::pluck('name', 'id')->toArray())
                        ->searchable(),
                ])
                ->query(function ($query, $data) {
                    return $data['eo_id']
                        ? $query->whereHas('ticket.event', fn($q) => $q->where('user_id', $data['eo_id']))
                        : $query;
                }),
        ];
    }

    protected function getTableContentFooter(): ?string
    {
        return null;
    }

    protected function getTableContentHeader(): ?string
    {
        return null;
    }

    protected function getTableHeaderActions(): array
    {
        return [
            // Time Period Filter Action
            \Filament\Tables\Actions\SelectAction::make('timeFilter')
                ->label('Periode')
                ->icon('heroicon-o-calendar')
                ->options([
                    '' => 'Semua Waktu',
                    'today' => 'Hari Ini',
                    'yesterday' => 'Kemarin',
                    'this_week' => 'Minggu Ini',
                    'last_week' => 'Minggu Lalu',
                    'this_month' => 'Bulan Ini',
                    'last_month' => 'Bulan Lalu',
                    'this_year' => 'Tahun Ini',
                ])
                ->action(function (string $value): void {
                    $this->timeFilter = $value;
                    $this->resetTableFiltersForm();
                }),
                
            // Status Filter Action
            \Filament\Tables\Actions\SelectAction::make('statusFilter')
                ->label('Status')
                ->icon('heroicon-o-funnel')
                ->options([
                    '' => 'Semua Status',
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                ])
                ->action(function (string $value): void {
                    $this->statusFilter = $value;
                    $this->resetTableFiltersForm();
                }),
                
            // Category Filter Action
            \Filament\Tables\Actions\SelectAction::make('categoryFilter')
                ->label('Kategori')
                ->icon('heroicon-o-tag')
                ->options(function () {
                    return ['' => 'Semua Kategori'] + \App\Models\Category::pluck('name', 'id')->toArray();
                })
                ->action(function (string $value): void {
                    $this->categoryFilter = $value;
                    $this->resetTableFiltersForm();
                }),
                
            Action::make('export')
                ->label('Export as CSV')
                ->icon('heroicon-o-arrow-down')
                ->url(function (): string {
                    $params = [];
                    
                    // Add time filter
                    if (!empty($this->timeFilter)) {
                        $params['time_period'] = $this->timeFilter;
                    }
                    
                    // Add status filter
                    if (!empty($this->statusFilter)) {
                        $params['status'] = $this->statusFilter;
                    }
                    
                    // Add category filter
                    if (!empty($this->categoryFilter)) {
                        $params['category_id'] = $this->categoryFilter;
                    }
                    
                    // Add event filter
                    if (!empty($this->eventFilter)) {
                        $params['event_id'] = $this->eventFilter;
                    }
                    
                    // Add table filters
                    $tableFilters = $this->tableFilters;
                    
                    // Add date range filters
                    if (!empty($tableFilters['date_range']['created_from'])) {
                        $params['created_from'] = $tableFilters['date_range']['created_from'];
                    }
                    
                    if (!empty($tableFilters['date_range']['created_until'])) {
                        $params['created_until'] = $tableFilters['date_range']['created_until'];
                    }
                    
                    // Add event organizer filter
                    if (!empty($tableFilters['eo_id']['eo_id'])) {
                        $params['eo_id'] = $tableFilters['eo_id']['eo_id'];
                    }
                    
                    // Use absolute URL to ensure proper routing
                    return url('/filament/reports/export?' . http_build_query($params));
                })
                ->openUrlInNewTab(),
        ];
    }

    protected function formatMoney($amount): string
    {
        return number_format($amount, 0, ',', '.');
    }

    protected function getWidgets(): array
    {
        return [
            InsightTrenWidget2::class,
            TopCategoriesWidget2::class,
            TransactionsPerWeekChart2::class,
            TopEventsWidget2::class,
            // ...widget lain jika perlu
        ];
    }
}
