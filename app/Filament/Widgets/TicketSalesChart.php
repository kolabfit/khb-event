<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;

class TicketSalesChart extends ChartWidget
{
    protected static ?string $heading = 'Ticket Sales (30 Days)';

    // instance, bukan static
    protected string|int|array $columnSpan = [
        'sm' => 2,
        'lg' => 'full',
    ];
    

    protected function getType(): string
    {
        return 'line';
    }
    

    protected function getData(): array
    {
        $records = Ticket::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('status','paid')
            ->where('created_at','>=', now()->subDays(29))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total','day')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Tickets Sold',
                    'data'            => array_values($records),
                    'borderColor'     => '#3B82F6',
                    'backgroundColor' => 'transparent',
                    'fill'            => false,
                ],
            ],
            'labels' => array_keys($records),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive'          => true,
            'maintainAspectRatio' => false,
            'scales'              => [
                'x' => ['grid'=>['display'=>false]],
                'y' => ['beginAtZero'=>true],
            ],
            'plugins'             => ['legend'=>['position'=>'top']],
            'elements'            => ['line'=>['tension'=>0.4]],
        ];
    }
}
