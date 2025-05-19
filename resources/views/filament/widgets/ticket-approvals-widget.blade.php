<x-filament::widget class="fi-wi-ticket-approvals">
    <x-filament::section>
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <x-filament::section.heading>
                    Ticket Approvals
                </x-filament::section.heading>
            </div>

            @if(count($selected) > 0)
                <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ count($selected) }} items selected
                    </span>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="approveSelected"
                            class="fi-btn relative flex items-center justify-center gap-x-2 rounded-lg bg-custom-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 h-8"
                            style="--c-400: var(--success-400); --c-500: var(--success-500); --c-600: var(--success-600);"
                        >
                            <x-filament::icon
                                icon="heroicon-s-check"
                                class="h-4 w-4"
                            />
                            <span>Approve selected</span>
                        </button>

                        <button
                            type="button"
                            wire:click="rejectSelected"
                            class="fi-btn relative flex items-center justify-center gap-x-2 rounded-lg bg-custom-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 h-8"
                            style="--c-400: var(--danger-400); --c-500: var(--danger-500); --c-600: var(--danger-600);"
                        >
                            <x-filament::icon
                                icon="heroicon-s-x-mark"
                                class="h-4 w-4"
                            />
                            <span>Reject selected</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-4">
            <div class="fi-ta-ctn">
                <div class="fi-ta-content">
                    <table class="fi-ta-table w-full table-auto text-start">
                        <thead class="bg-transparent">
                            <tr class="fi-ta-tr">
                                <th class="fi-ta-header-cell p-0 w-8">
                                    <div class="px-3 py-2">
                                        <label>
                                            <input
                                                type="checkbox"
                                                class="fi-checkbox rounded border-gray-300 text-primary-600 shadow-sm transition duration-75 focus:ring-2 focus:ring-primary-600 focus:ring-offset-0 disabled:opacity-70 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-primary-500 dark:focus:ring-primary-600"
                                                wire:model.live="selectAll"
                                            >
                                            <span class="sr-only">Select all</span>
                                        </label>
                                    </div>
                                </th>
                                <th class="fi-ta-header-cell p-0">
                                    <button class="flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-black dark:text-white font-semibold">
                                        <span>ID</span>
                                    </button>
                                </th>
                                <th class="fi-ta-header-cell p-0">
                                    <button class="flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-black dark:text-white font-semibold">
                                        <span>Tickets</span>
                                    </button>
                                </th>
                                <th class="fi-ta-header-cell p-0">
                                    <button class="flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-black dark:text-white font-semibold">
                                        <span>Event</span>
                                    </button>
                                </th>
                                <th class="fi-ta-header-cell p-0">
                                    <button class="flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-black dark:text-white font-semibold">
                                        <span>Pemesan</span>
                                    </button>
                                </th>
                                <th class="fi-ta-header-cell p-0 min-w-[140px]">
                                    <button class="flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-black dark:text-white font-semibold">
                                        <span>Jumlah</span>
                                    </button>
                                </th>
                                <th class="fi-ta-header-cell p-0">
                                    <button class="flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-black dark:text-white font-semibold">
                                        <span>Metode</span>
                                    </button>
                                </th>
                                <th class="fi-ta-header-cell p-0 min-w-[120px]">
                                    <button class="flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-black dark:text-white font-semibold">
                                        <span>Sumber</span>
                                    </button>
                                </th>
                                <th class="fi-ta-header-cell p-0">
                                    <button class="flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-black dark:text-white font-semibold">
                                        <span>Receipt</span>
                                    </button>
                                </th>
                                <th class="fi-ta-header-cell p-0">
                                    <button class="flex w-full items-center gap-x-1 px-4 py-2 text-sm font-medium text-black dark:text-white font-semibold">
                                        <span>Dibuat</span>
                                    </button>
                                </th>
                                <th class="fi-ta-header-cell w-1">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-900">
                            @forelse($pendingPayments as $payment)
                                <tr class="fi-ta-row hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                                    <td class="fi-ta-cell px-3 py-2.5">
                                        <label>
                                            <input
                                                type="checkbox"
                                                class="fi-checkbox rounded border-gray-300 text-primary-600 shadow-sm transition duration-75 focus:ring-2 focus:ring-primary-600 focus:ring-offset-0 disabled:opacity-70 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-primary-500 dark:focus:ring-primary-600"
                                                wire:model.live="selected"
                                                value="{{ $payment->id }}"
                                            >
                                            <span class="sr-only">Select payment #{{ $payment->id }}</span>
                                        </label>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5">
                                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            {{ $payment->id }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5">
                                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            {{ $payment->tickets()->count() }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5">
                                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            {{ $payment->tickets()->first()?->event?->title ?? 'Unknown Event' }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5">
                                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            {{ $payment->tickets()->first()?->user?->name ?? 'Unknown User' }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5 min-w-[140px]">
                                        <div class="flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 whitespace-nowrap">
                                            Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5">
                                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            {{ $payment->method }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5 min-w-[120px]">
                                        <div class="flex items-center gap-2">
                                            @if($payment->receipt_path)
                                                <x-filament::badge color="info" class="text-xs">
                                                    User
                                                </x-filament::badge>
                                            @else
                                                <x-filament::badge color="gray" class="text-xs">
                                                    Admin
                                                </x-filament::badge>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5">
                                        @if($payment->receipt_path)
                                            <div class="flex items-center justify-center">
                                                <button
                                                    type="button"
                                                    class="fi-btn fi-btn-size-sm relative inline-flex items-center justify-center font-medium hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 ring-offset-2 ring-offset-white dark:ring-offset-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-400 shadow-sm fi-color-custom h-8 w-8"
                                                    x-data
                                                    x-on:click="$dispatch('open-modal', { id: 'view-receipt-{{ $payment->id }}' })"
                                                >
                                                    <x-filament::icon
                                                        icon="heroicon-m-eye"
                                                        class="h-4 w-4"
                                                    />
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5">
                                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            {{ $payment->created_at->format('d M Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-4 py-2.5">
                                        <div class="flex items-center justify-center gap-2">
                                            <button
                                                type="button"
                                                class="fi-btn relative flex h-8 w-8 items-center justify-center rounded-lg bg-custom-600 text-white shadow-sm hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400"
                                                style="--c-400: var(--success-400); --c-500: var(--success-500); --c-600: var(--success-600);"
                                                wire:click="approve({{ $payment->id }})"
                                                wire:loading.attr="disabled"
                                            >
                                                <x-filament::icon
                                                    icon="heroicon-s-check"
                                                    class="h-4 w-4"
                                                />
                                            </button>
                                            
                                            <button
                                                type="button"
                                                class="fi-btn relative flex h-8 w-8 items-center justify-center rounded-lg bg-custom-600 text-white shadow-sm hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400"
                                                style="--c-400: var(--danger-400); --c-500: var(--danger-500); --c-600: var(--danger-600);"
                                                wire:click="reject({{ $payment->id }})"
                                                wire:loading.attr="disabled"
                                            >
                                                <x-filament::icon
                                                    icon="heroicon-s-x-mark"
                                                    class="h-4 w-4"
                                                />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                @if($payment->receipt_path)
                                    <x-filament::modal id="view-receipt-{{ $payment->id }}" width="xl">
                                        <x-slot name="header">
                                            <x-filament::modal.heading>
                                                Bukti Pembayaran #{{ $payment->id }}
                                            </x-filament::modal.heading>
                                        </x-slot>

                                        <div class="p-4">
                                            <div class="relative aspect-[3/4] bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden">
                                                <img 
                                                    src="{{ Storage::url($payment->receipt_path) }}"
                                                    alt="Bukti Pembayaran #{{ $payment->id }}"
                                                    class="h-full w-full object-contain"
                                                />
                                            </div>
                                        </div>

                                        <x-slot name="footer">
                                            <div class="flex justify-end gap-x-2">
                                                <x-filament::button
                                                    x-on:click="close"
                                                    color="gray"
                                                >
                                                    Tutup
                                                </x-filament::button>
                                            </div>
                                        </x-slot>
                                    </x-filament::modal>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="flex flex-col items-center justify-center px-6 py-12">
                                            <div class="mb-4 rounded-full bg-gray-100/80 p-3 dark:bg-gray-800/50">
                                                <x-filament::icon
                                                    icon="heroicon-o-ticket"
                                                    class="h-6 w-6 text-gray-400 dark:text-gray-500"
                                                />
                                            </div>

                                            <h3 class="text-base font-medium text-gray-900 dark:text-white">
                                                No pending payments
                                            </h3>

                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                There are no payments waiting for approval at the moment.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pendingPayments->hasPages())
                    <div class="fi-ta-pagination-ctn px-3 py-3 sm:px-6">
                        <div class="fi-pagination flex items-center justify-between gap-4">
                            {{ $pendingPayments->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament::widget> 