@php
    $label = $label ?? 'Subtotal';
@endphp

<div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 filament-tables-footer">
    <div class="flex items-center justify-end gap-4">
        <span class="text-sm font-medium dark:text-white">
            {{ $label }}:
        </span>
        <span class="text-xl font-bold text-primary-600 dark:text-primary-400">
            Rp {{ $total }}
        </span>
    </div>
</div> 