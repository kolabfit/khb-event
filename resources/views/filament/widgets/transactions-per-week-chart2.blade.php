<x-filament::widget>
    <h2 class="text-lg font-bold mb-4">Transaksi per Minggu (8 Minggu Terakhir)</h2>
    <div x-data x-init="
        new Chart($refs.canvas, {
            type: 'bar',
            data: {
                labels: @js($labels),
                datasets: [{
                    label: 'Transaksi',
                    data: @js($data),
                    backgroundColor: '#34D399',
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