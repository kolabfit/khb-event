{{-- resources/views/filament/pages/report-page.blade.php --}}
@php
    /** @var \App\Filament\Pages\ReportPage $this */
@endphp

<x-filament::page>
    <div class="space-y-6">
        {{-- Header Section --}}
        <div>
            <p class="mt-1 text-gray-500">Pantau transaksi dan pendapatan event Anda secara real-time.</p>
        </div>

        {{-- Statistics Grid --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            {{-- Total Transactions Card --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex flex-col p-6">
                    <div class="flex items-center gap-x-2">
                        <div class="rounded-lg bg-primary-50 p-2 dark:bg-primary-500/10">
                            <x-heroicon-o-receipt-refund class="h-5 w-5 text-primary-500 dark:text-primary-400" />
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transaksi</span>
                    </div>
                    <div class="mt-4 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        {{ $this->getViewData()['totalTransactions'] }}
                    </div>
                </div>
            </div>

            {{-- Total Revenue Card --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex flex-col p-6">
                    <div class="flex items-center gap-x-2">
                        <div class="rounded-lg bg-success-50 p-2 dark:bg-success-500/10">
                            <x-heroicon-o-currency-dollar class="h-5 w-5 text-success-500 dark:text-success-400" />
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</span>
                    </div>
                    <div class="mt-4 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        Rp {{ $this->formatMoney($this->getViewData()['totalRevenue']) }}
                    </div>
                </div>
            </div>

            {{-- Total Events Card --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex flex-col p-6">
                    <div class="flex items-center gap-x-2">
                        <div class="rounded-lg bg-warning-50 p-2 dark:bg-warning-500/10">
                            <x-heroicon-o-calendar class="h-5 w-5 text-warning-500 dark:text-warning-400" />
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Event</span>
                    </div>
                    <div class="mt-4 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        {{ $this->getViewData()['totalEvents'] }}
                    </div>
                </div>
            </div>

            {{-- Total Users Card --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex flex-col p-6">
                    <div class="flex items-center gap-x-2">
                        <div class="rounded-lg bg-info-50 p-2 dark:bg-info-500/10">
                            <x-heroicon-o-user-group class="h-5 w-5 text-info-500 dark:text-info-400" />
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total User</span>
                    </div>
                    <div class="mt-4 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        {{ $this->getViewData()['totalUsers'] }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Analytic Widgets --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @livewire(\App\Filament\ReportWidgets\TopCategoriesWidget2::class)
            @livewire(\App\Filament\ReportWidgets\TransactionsPerWeekChart2::class)
        </div>

        <div class="mt-6">
            @livewire(\App\Filament\ReportWidgets\TopEventsWidget2::class)
        </div>

        {{-- Table Section --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            {{ $this->table }}
        </div>
    </div>
</x-filament::page>
