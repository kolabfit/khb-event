{{-- resources/views/filament/pages/report-page.blade.php --}}
@php
    /** @var \App\Filament\Pages\ReportPage $this */
@endphp

<x-filament::page>
    <div class="space-y-4">
        {{-- Panggil render() di instance table --}}
        {!! $this->table->render() !!}
    </div>
</x-filament::page>
