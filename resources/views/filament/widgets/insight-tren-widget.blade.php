<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="rounded-xl bg-white shadow p-4 dark:bg-gray-900">
        <div class="flex items-center gap-2">
            <span class="text-lg font-semibold">Transaksi Minggu Ini</span>
            @if($countChange > 0)
                <span class="text-green-600 flex items-center"><x-heroicon-o-arrow-trending-up class="w-4 h-4"/> +{{ $countChange }}%</span>
            @elseif($countChange < 0)
                <span class="text-red-600 flex items-center"><x-heroicon-o-arrow-trending-down class="w-4 h-4"/> {{ $countChange }}%</span>
            @else
                <span class="text-gray-500">0%</span>
            @endif
        </div>
        <div class="text-3xl font-bold mt-2">{{ $thisWeekCount }}</div>
        <div class="text-xs text-gray-500">Minggu lalu: {{ $lastWeekCount }}</div>
    </div>
    <div class="rounded-xl bg-white shadow p-4 dark:bg-gray-900">
        <div class="flex items-center gap-2">
            <span class="text-lg font-semibold">Pendapatan Minggu Ini</span>
            @if($revenueChange > 0)
                <span class="text-green-600 flex items-center"><x-heroicon-o-arrow-trending-up class="w-4 h-4"/> +{{ $revenueChange }}%</span>
            @elseif($revenueChange < 0)
                <span class="text-red-600 flex items-center"><x-heroicon-o-arrow-trending-down class="w-4 h-4"/> {{ $revenueChange }}%</span>
            @else
                <span class="text-gray-500">0%</span>
            @endif
        </div>
        <div class="text-3xl font-bold mt-2">Rp {{ number_format($thisWeekRevenue, 0, ',', '.') }}</div>
        <div class="text-xs text-gray-500">Minggu lalu: Rp {{ number_format($lastWeekRevenue, 0, ',', '.') }}</div>
    </div>
</div> 