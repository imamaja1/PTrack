<?php

use Livewire\Volt\Component;
use App\Services\ReportService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $month;

    protected ReportService $reportService;

    public function boot(ReportService $reportService): void
    {
        $this->reportService = $reportService;
    }

    public function mount(): void
    {
        $this->month = Carbon::now()->format('Y-m');
    }

    public function updatedMonth(): void
    {
        $this->dispatch('report-data-updated');
    }

    public function getChartData(): array
    {
        return $this->reportService->getChartData(auth()->id(), $this->month);
    }
}; ?>

<div>
    <x-slot name="header">
        <flux:heading size="xl" level="1">Laporan Keuangan</flux:heading>
    </x-slot>

    <div class="space-y-6" x-data>
        
        <div class="flex justify-between items-center mb-6">
            <flux:heading size="lg">Distribusi Kategori</flux:heading>
            <div class="w-48">
                <flux:input type="month" wire:model.live="month" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Expense Chart -->
            <flux:card>
                <flux:heading size="md" class="text-center mb-4">Pengeluaran Bulan Ini</flux:heading>
                <div class="w-full max-w-sm mx-auto aspect-square relative">
                    <canvas id="expensePieChart"></canvas>
                </div>
            </flux:card>

            <!-- Income Chart -->
            <flux:card>
                <flux:heading size="md" class="text-center mb-4">Pemasukan Bulan Ini</flux:heading>
                <div class="w-full max-w-sm mx-auto aspect-square relative">
                    <canvas id="incomePieChart"></canvas>
                </div>
            </flux:card>
        </div>

    @script
    <script>
        let expenseChart = null;
        let incomeChart = null;

        async function renderCharts() {
            const data = await $wire.getChartData();
            
            const ctxExpense = document.getElementById('expensePieChart');
            if (ctxExpense) {
                if (expenseChart) expenseChart.destroy();
                expenseChart = new Chart(ctxExpense, {
                    type: 'doughnut',
                    data: {
                        labels: data.expense.labels,
                        datasets: [{
                            data: data.expense.data,
                            backgroundColor: data.expense.colors,
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if(!data.expense.hasData) return ' Tidak ada data';
                                        let label = context.label || '';
                                        if (label) label += ': ';
                                        label += 'Rp ' + context.raw.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            const ctxIncome = document.getElementById('incomePieChart');
            if (ctxIncome) {
                if (incomeChart) incomeChart.destroy();
                incomeChart = new Chart(ctxIncome, {
                    type: 'doughnut',
                    data: {
                        labels: data.income.labels,
                        datasets: [{
                            data: data.income.data,
                            backgroundColor: data.income.colors,
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if(!data.income.hasData) return ' Tidak ada data';
                                        let label = context.label || '';
                                        if (label) label += ': ';
                                        label += 'Rp ' + context.raw.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        renderCharts();

        $wire.on('report-data-updated', () => {
            renderCharts();
        });
    </script>
    @endscript
</div>
