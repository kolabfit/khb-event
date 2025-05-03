<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Event;
use App\Models\Ticket;
use Carbon\CarbonPeriod;

class TicketSalesChart extends Component
{
    public string $groupBy = 'daily';  // default filter

    public function render()
    {
        $now = now();

        // 1) Hitung periode & labels
        if ($this->groupBy === 'weekly') {
            $start  = $now->copy()->subWeeks(11)->startOfWeek();
            $period = CarbonPeriod::since($start)->weeks(1)->until($now);
            $labels = collect($period)
                ->map(fn($d) => 'Wk '.$d->weekOfYear.' '.$d->format('M'))
                ->toArray();
        } elseif ($this->groupBy === 'monthly') {
            $start  = $now->copy()->subMonths(5)->startOfMonth();
            $period = CarbonPeriod::since($start)->months(1)->until($now);
            $labels = collect($period)
                ->map(fn($d) => $d->format('M Y'))
                ->toArray();
        } else {
            $start  = $now->copy()->subDays(29)->startOfDay();
            $period = CarbonPeriod::since($start)->days(1)->until($now);
            $labels = collect($period)
                ->map(fn($d) => $d->format('d M'))
                ->toArray();
        }

        // 2) Cari top 5 event_id by paid tickets
        $top5 = Ticket::query()
            ->where('status', 'paid')
            ->where('created_at','>=', $start)
            ->groupBy('event_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->pluck(\DB::raw('COUNT(*)'), 'event_id');

        $events = Event::whereIn('id', $top5->keys()->toArray())
                       ->get()
                       ->keyBy('id');

        $palette = ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6'];

        // 3) Build datasets
        $datasets = [];
        foreach ($top5->keys()->values() as $i => $eventId) {
            $color = $palette[$i] ?? $palette[0];
            $title = $events[$eventId]->title;

            $data = collect($period)
                ->map(fn($date) => Ticket::where('event_id', $eventId)
                    ->where('status','paid')
                    ->when($this->groupBy==='weekly',
                        fn($q) => $q->whereRaw("YEARWEEK(created_at,1)=?", [$date->format('oW')])
                    )
                    ->when($this->groupBy==='monthly',
                        fn($q) => $q->whereMonth('created_at',$date->month)
                                   ->whereYear('created_at',$date->year)
                    )
                    ->when($this->groupBy==='daily',
                        fn($q) => $q->whereDate('created_at',$date->format('Y-m-d'))
                    )
                    ->count()
                )
                ->toArray();

            $datasets[] = [
                'label' => $title,
                'data'  => $data,
                'borderColor'     => $color,
                'backgroundColor' => 'transparent',
                'fill'            => false,
            ];
        }

        return view('livewire.ticket-sales-chart', [
            'labels'   => $labels,
            'datasets' => $datasets,
        ]);
    }
}
