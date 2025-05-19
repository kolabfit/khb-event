{{-- resources/views/filament/pages/transaction-detail.blade.php --}}
<x-filament::page>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="rounded-xl bg-white shadow p-6 dark:bg-gray-900">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <x-heroicon-o-eye class="h-6 w-6 text-primary-500" />
                Detail Transaksi
            </h2>
            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-500">ID Transaksi</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->id }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-500">Tanggal</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-500">Status</dt>
                    <dd>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                            @if($payment->status === 'paid') bg-green-100 text-green-800 @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-500">Nominal</dt>
                    <dd class="text-gray-900 dark:text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-500">Metode Pembayaran</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->method }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-500">Pembeli</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->ticket->user->name ?? '-' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-500">Event</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->ticket->event->title ?? '-' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-500">Event Organizer</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $payment->ticket->event->user->name ?? '-' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-500">Kategori Event</dt>
                    <dd class="text-gray-900 dark:text-white">
                        @foreach($payment->ticket->event->categories ?? [] as $cat)
                            <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $cat->name }}</span>
                        @endforeach
                    </dd>
                </div>
            </dl>
        </div>
        <div>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-2" />
                Kembali
            </a>
        </div>
    </div>
</x-filament::page> 