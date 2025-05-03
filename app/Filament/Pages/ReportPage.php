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
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;


class ReportPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.report-page';

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected function getTableQuery(): Builder
    {
        return Payment::query()
            ->with(['ticket.event.categories', 'ticket.user'])
            ->orderBy('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('ticket.id')->label('Ticket ID'),
            Tables\Columns\TextColumn::make('ticket.event.title')->label('Event'),
            Tables\Columns\TextColumn::make('ticket.user.name')->label('User'),
            Tables\Columns\TextColumn::make('amount')->label('Amount')->money('idr', true),
            Tables\Columns\TextColumn::make('method')->label('Method'),
            Tables\Columns\BadgeColumn::make('status')
                ->colors(['warning' => 'pending', 'success' => 'paid', 'danger' => 'failed']),
            Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d M Y H:i'),
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

            // 2) Status pembayaran – cukup opsi saja
            SelectFilter::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                ]),

            Filter::make('category_id')
                ->label('Kategori')
                ->form([
                    Select::make('category_id')
                        ->label('Pilih Kategori')
                        ->options(\App\Models\Category::pluck('name', 'id')->toArray())
                        ->searchable(),
                ])
                ->query(function ($query, $data) {
                    return $data['category_id']
                        ? $query->whereHas('ticket.event.categories', fn($q) => $q->where('id', $data['category_id']))
                        : $query;
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

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export as CSV')
                ->icon('heroicon-o-arrow-down')
                ->url(fn(): string => route('report.export', request()->query()))
                ->openUrlInNewTab(),
        ];
    }
}
