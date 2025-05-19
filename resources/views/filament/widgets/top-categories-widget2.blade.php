<x-filament::widget>
    <h2 class="text-lg font-bold mb-4">Top 5 Kategori Terlaris</h2>
    <div x-data x-init="
        new Chart($refs.canvas, {
            type: 'bar',
            data: {
                labels: @js($categories->pluck('name')),
                datasets: [{
                    label: 'Transaksi Lunas',
                    data: @js($categories->pluck('paid_transactions_count')),
                    backgroundColor: '#f59e42',
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    ">
        <canvas x-ref="canvas" height="120"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-filament::widget>