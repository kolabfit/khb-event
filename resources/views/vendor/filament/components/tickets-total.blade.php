@php
    $label = $label ?? 'Subtotal';
@endphp

<div class="px-4 py-3 filament-tables-footer">
    <div class="flex items-center justify-end gap-4">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
            {{ $label }}
        </span>
        <div class="flex items-center gap-1">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Rp</span>
            <span class="text-xl font-bold tracking-tight text-primary-500 dark:text-primary-400">
                {{ $total }}
            </span>
        </div>
    </div>
</div> 