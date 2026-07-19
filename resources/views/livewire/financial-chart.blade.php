<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Transaction;
use Carbon\Carbon;

new class extends Component {
    public $filterType = 'year'; // 'year', 'month' or 'week'
    public $selectedYear;
    public $selectedMonth;
    public $selectedWeek;

    public $chartData = [];
    public $yearOptions = [];
    public $monthOptions = [];
    public $weekOptions = [];
    public $insights = [];

    public function mount()
    {
        Carbon::setLocale('id'); // Ensure Indonesian locale for translatedFormat
        $this->selectedYear = Carbon::now()->format('Y');
        $this->selectedMonth = Carbon::now()->format('Y-m');
        $this->selectedWeek = Carbon::now()->format('o-W'); // ISO Year and ISO Week
        
        $this->generateOptions();
        $this->loadChartData();
    }

    public function updatedFilterType() { $this->loadChartData(); }
    public function updatedSelectedYear() { $this->loadChartData(); }
    public function updatedSelectedMonth() { $this->loadChartData(); }
    public function updatedSelectedWeek() { $this->loadChartData(); }

    public function generateOptions()
    {
        // Generate last 5 years
        for ($i = 0; $i < 5; $i++) {
            $year = Carbon::now()->subYears($i)->format('Y');
            $this->yearOptions[$year] = $year;
        }
        // Generate last 12 months for month options
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $this->monthOptions[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }

        // Generate last 12 weeks for week options
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subWeeks($i);
            $start = $date->copy()->startOfWeek();
            $end = $date->copy()->endOfWeek();
            $this->weekOptions[$date->format('o-W')] = $start->format('d M') . ' - ' . $end->format('d M Y');
        }
    }

    #[On('transaction-updated')]
    public function loadChartData()
    {
        $userId = auth()->id();
        $labels = [];
        $incomeData = [];
        $expenseData = [];

        if ($this->filterType === 'year') {
            $startOfYear = Carbon::createFromDate($this->selectedYear, 1, 1)->startOfYear();
            $endOfYear = $startOfYear->copy()->endOfYear();

            $transactions = Transaction::where('user_id', $userId)
                ->whereBetween('transaction_date', [$startOfYear->format('Y-m-d'), $endOfYear->format('Y-m-d')])
                ->get();

            $monthly = array_fill(1, 12, ['income' => 0, 'expense' => 0]);
            foreach ($transactions as $tx) {
                $month = (int) Carbon::parse($tx->transaction_date)->format('m');
                if ($tx->type === 'income') {
                    $monthly[$month]['income'] += $tx->amount;
                } else {
                    $monthly[$month]['expense'] += $tx->amount;
                }
            }

            for ($i = 1; $i <= 12; $i++) {
                $date = Carbon::createFromDate($this->selectedYear, $i, 1);
                $labels[] = $date->translatedFormat('M Y');
                $incomeData[] = $monthly[$i]['income'];
                $expenseData[] = $monthly[$i]['expense'];
            }

        } elseif ($this->filterType === 'month') {
            $date = Carbon::createFromFormat('Y-m', $this->selectedMonth)->startOfMonth();
            $startOfMonth = $date->copy();
            $endOfMonth = $date->copy()->endOfMonth();

            $transactions = Transaction::where('user_id', $userId)
                ->whereBetween('transaction_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->get();

            $chunks = [];
            $current = $startOfMonth->copy();
            $weekNumber = 1;

            while ($current <= $endOfMonth) {
                $endOfWeek = $current->copy()->addDays(6);
                if ($endOfWeek > $endOfMonth) {
                    $endOfWeek = $endOfMonth->copy();
                }

                $chunks[$weekNumber] = [
                    'label' => "Minggu $weekNumber (" . $current->format('d') . "-" . $endOfWeek->format('d') . " " . $current->translatedFormat('M') . ")",
                    'start' => $current->format('Y-m-d'),
                    'end' => $endOfWeek->format('Y-m-d'),
                    'income' => 0,
                    'expense' => 0
                ];

                $current->addDays(7);
                $weekNumber++;
            }

            foreach ($transactions as $tx) {
                $txDate = Carbon::parse($tx->transaction_date)->format('Y-m-d');
                foreach ($chunks as $idx => $chunk) {
                    if ($txDate >= $chunk['start'] && $txDate <= $chunk['end']) {
                        if ($tx->type === 'income') {
                            $chunks[$idx]['income'] += $tx->amount;
                        } else {
                            $chunks[$idx]['expense'] += $tx->amount;
                        }
                        break;
                    }
                }
            }

            foreach ($chunks as $chunk) {
                $labels[] = $chunk['label'];
                $incomeData[] = $chunk['income'];
                $expenseData[] = $chunk['expense'];
            }

        } else {
            list($year, $week) = explode('-', $this->selectedWeek);
            $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();

            $transactions = Transaction::where('user_id', $userId)
                ->whereBetween('transaction_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->get();

            $weekly = array_fill(1, 7, ['income' => 0, 'expense' => 0]);
            foreach ($transactions as $tx) {
                $dayOfWeek = Carbon::parse($tx->transaction_date)->dayOfWeekIso; // 1 to 7
                if ($tx->type === 'income') {
                    $weekly[$dayOfWeek]['income'] += $tx->amount;
                } else {
                    $weekly[$dayOfWeek]['expense'] += $tx->amount;
                }
            }

            $currentDate = $startOfWeek->copy();
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            for ($i = 0; $i < 7; $i++) {
                $labels[] = $days[$i] . ' (' . $currentDate->format('d M') . ')';
                $incomeData[] = $weekly[$i+1]['income'];
                $expenseData[] = $weekly[$i+1]['expense'];
                $currentDate->addDay();
            }
        }

        $this->chartData = [
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData,
        ];
        
        $this->calculateInsights($labels, $incomeData, $expenseData);
        
        $this->dispatch('chart-data-updated');
    }

    public function calculateInsights($labels, $incomeData, $expenseData)
    {
        $maxIncome = !empty($incomeData) ? max($incomeData) : 0;
        $minIncome = !empty($incomeData) ? min($incomeData) : 0;
        
        $maxIncomeIndex = !empty($incomeData) ? array_keys($incomeData, $maxIncome)[0] : 0;
        $minIncomeIndex = !empty($incomeData) ? array_keys($incomeData, $minIncome)[0] : 0;
        
        $maxExpense = !empty($expenseData) ? max($expenseData) : 0;
        $maxExpenseIndex = !empty($expenseData) ? array_keys($expenseData, $maxExpense)[0] : 0;
        
        $minExpense = !empty($expenseData) ? min($expenseData) : 0;
        $minExpenseIndex = !empty($expenseData) ? array_keys($expenseData, $minExpense)[0] : 0;
        
        $this->insights = [
            'maxIncome' => ['amount' => $maxIncome, 'label' => $labels[$maxIncomeIndex] ?? '-'],
            'minIncome' => ['amount' => $minIncome, 'label' => $labels[$minIncomeIndex] ?? '-'],
            'maxExpense' => ['amount' => $maxExpense, 'label' => $labels[$maxExpenseIndex] ?? '-'],
            'minExpense' => ['amount' => $minExpense, 'label' => $labels[$minExpenseIndex] ?? '-'],
        ];
    }
}; ?>

<div x-data="{
        initCharts() {
            // Income Chart
            const incomeCanvas = document.getElementById('incomeChart');
            if (incomeCanvas) {
                const ctx1 = incomeCanvas.getContext('2d');
                if (incomeCanvas.chart) incomeCanvas.chart.destroy();
                incomeCanvas.chart = new Chart(ctx1, {
                    type: 'bar',
                    data: this.getIncomeChartData(),
                    options: this.getChartOptions()
                });
            }

            // Expense Chart
            const expenseCanvas = document.getElementById('expenseChart');
            if (expenseCanvas) {
                const ctx2 = expenseCanvas.getContext('2d');
                if (expenseCanvas.chart) expenseCanvas.chart.destroy();
                expenseCanvas.chart = new Chart(ctx2, {
                    type: 'bar',
                    data: this.getExpenseChartData(),
                    options: this.getChartOptions()
                });
            }
        },
        getChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) { label += 'Rp ' + context.parsed.y.toLocaleString('id-ID'); }
                                return label;
                            }
                        }
                    }
                }
            };
        },
        getIncomeChartData() {
            const rawData = $wire.chartData;
            return {
                labels: [...rawData.labels],
                datasets: [{
                    label: 'Pemasukan',
                    data: [...rawData.income],
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderRadius: 4,
                }]
            };
        },
        getExpenseChartData() {
            const rawData = $wire.chartData;
            return {
                labels: [...rawData.labels],
                datasets: [{
                    label: 'Pengeluaran',
                    data: [...rawData.expense],
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderRadius: 4,
                }]
            };
        },
        updateCharts() {
            if (typeof Chart === 'undefined') return;
            
            const incomeCanvas = document.getElementById('incomeChart');
            const expenseCanvas = document.getElementById('expenseChart');

            if (incomeCanvas && incomeCanvas.chart && expenseCanvas && expenseCanvas.chart) {
                incomeCanvas.chart.data = this.getIncomeChartData();
                incomeCanvas.chart.update();
                
                expenseCanvas.chart.data = this.getExpenseChartData();
                expenseCanvas.chart.update();
            } else {
                this.initCharts();
            }
        }
    }"
    x-init="initCharts()"
    @transaction-updated.window="$wire.loadChartData()"
    @chart-data-updated.window="updateCharts()"
>
    <!-- Filter Card -->
    <flux:card class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <flux:heading size="lg">Laporan Keuangan</flux:heading>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <!-- Filter Type -->
                <flux:select wire:model.live="filterType" class="w-full sm:w-40 min-w-40">
                    <option value="year">Per Tahun</option>
                    <option value="month">Per Bulan</option>
                    <option value="week">Per Minggu</option>
                </flux:select>
                
                <!-- Contextual Filter -->
                @if($filterType === 'year')
                    <flux:select wire:model.live="selectedYear" class="w-full sm:w-40 min-w-40">
                        @foreach($yearOptions as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                @elseif($filterType === 'month')
                    <flux:select wire:model.live="selectedMonth" class="w-full sm:w-56 min-w-56">
                        @foreach($monthOptions as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:select wire:model.live="selectedWeek" class="w-full sm:w-64 min-w-64">
                        @foreach($weekOptions as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                @endif
            </div>
        </div>
    </flux:card>

    <!-- Insights KPI Cards -->
    @if(!empty($insights))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Max Income -->
        <flux:card class="bg-green-50/50 dark:bg-green-900/10 border border-green-100 dark:border-green-900/30">
            <div class="text-sm text-green-600 dark:text-green-400 font-medium mb-1">Pemasukan Tertinggi</div>
            <div class="text-xl font-semibold text-gray-900 dark:text-white mb-1">Rp {{ number_format($insights['maxIncome']['amount'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500">{{ $insights['maxIncome']['label'] }}</div>
        </flux:card>
        
        <!-- Max Expense -->
        <flux:card class="bg-red-50/50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30">
            <div class="text-sm text-red-600 dark:text-red-400 font-medium mb-1">Pengeluaran Tertinggi</div>
            <div class="text-xl font-semibold text-gray-900 dark:text-white mb-1">Rp {{ number_format($insights['maxExpense']['amount'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500">{{ $insights['maxExpense']['label'] }}</div>
        </flux:card>

        <!-- Min Income -->
        <flux:card class="bg-emerald-50/50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/30">
            <div class="text-sm text-emerald-600 dark:text-emerald-400 font-medium mb-1">Pemasukan Terendah</div>
            <div class="text-xl font-semibold text-gray-900 dark:text-white mb-1">Rp {{ number_format($insights['minIncome']['amount'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500">{{ $insights['minIncome']['label'] }}</div>
        </flux:card>

        <!-- Min Expense -->
        <flux:card class="bg-rose-50/50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30">
            <div class="text-sm text-rose-600 dark:text-rose-400 font-medium mb-1">Pengeluaran Terendah</div>
            <div class="text-xl font-semibold text-gray-900 dark:text-white mb-1">Rp {{ number_format($insights['minExpense']['amount'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500">{{ $insights['minExpense']['label'] }}</div>
        </flux:card>
    </div>
    @endif

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Income Chart -->
        <flux:card>
            <flux:heading size="md" class="mb-4">Grafik Pemasukan</flux:heading>
            <div class="relative h-72 w-full" wire:ignore>
                <canvas id="incomeChart"></canvas>
            </div>
        </flux:card>
        
        <!-- Expense Chart -->
        <flux:card>
            <flux:heading size="md" class="mb-4">Grafik Pengeluaran</flux:heading>
            <div class="relative h-72 w-full" wire:ignore>
                <canvas id="expenseChart"></canvas>
            </div>
        </flux:card>
    </div>
</div>
