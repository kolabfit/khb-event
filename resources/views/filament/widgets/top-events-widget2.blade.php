<x-filament::widget>
    <h2 class="text-lg font-bold mb-4">Top 5 Events</h2>
    <table class="w-full text-sm border rounded overflow-hidden">
        <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
                <th class="px-3 py-2 text-left">Event</th>
                <th class="px-3 py-2 text-left">Tickets</th>
                <th class="px-3 py-2 text-left">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr class="border-b dark:border-gray-700">
                    <td class="px-3 py-2 flex items-center gap-3">
                        <img
                            src="{{ asset('storage/' . ltrim($event->thumbnail, '/')) }}"
                            alt="{{ $event->title }}"
                            class="w-32 h-20 object-cover rounded shadow"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($event->title) }}&background=random';"
                        >
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ $event->title }}</span>
                    </td>
                    <td class="px-3 py-2">{{ $event->tickets_count }}</td>
                    <td class="px-3 py-2">Rp {{ number_format($event->payments_sum_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-filament::widget>