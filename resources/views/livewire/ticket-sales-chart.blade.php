<div class="bg-white shadow rounded p-4">
    {{-- Header: Judul + Dropdown --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Ticket Sales</h3>
        <select wire:model="groupBy"
                class="border rounded px-2 py-1 text-sm"
        >
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
        </select>
    </div>

    {{-- Chart.js Canvas --}}
    <div class="relative h-64">
        <canvas id="ticketSalesChart"></canvas>
    </div>

    {{-- Init & re-render --}}
    <script>
        document.addEventListener("livewire:load", function() {
            window.ticketChart = new Chart(
                document.getElementById('ticketSalesChart'),
                {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: @json($datasets),
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true },
                        },
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: { mode: 'index', intersect: false },
                        },
                        elements: {
                            line: { tension: 0.4 },
                            point: { radius: 4 },
                        },
                    },
                }
            );

            Livewire.hook('message.processed', () => {
                if (window.ticketChart) {
                    window.ticketChart.destroy();
                }
                window.ticketChart = new Chart(
                    document.getElementById('ticketSalesChart'),
                    {
                        type: 'line',
                        data: {
                            labels: @json($labels),
                            datasets: @json($datasets),
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: { grid: { display: false } },
                                y: { beginAtZero: true },
                            },
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: { mode: 'index', intersect: false },
                            },
                            elements: {
                                line: { tension: 0.4 },
                                point: { radius: 4 },
                            },
                        },
                    }
                );
            });
        });
    </script>
</div>
