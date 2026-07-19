<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;

new class extends Component {
    public $currentBalance = 0;
    public $monthlyIncome = 0;
    public $monthlyExpense = 0;

    public function mount()
    {
        $this->loadSummary();
    }

    #[On('transaction-updated')]
    public function loadSummary()
    {
        $userId = auth()->id();
        $now = Carbon::now();

        $this->currentBalance = \App\Models\Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount') 
            - \App\Models\Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');

        $this->monthlyIncome = \App\Models\Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->where('type', 'income')
            ->sum('amount');

        $this->monthlyExpense = \App\Models\Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->where('type', 'expense')
            ->sum('amount');
    }
}; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Current Balance -->
    <flux:card class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-zinc-500 mb-1">Saldo Saat Ini</p>
            <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">Rp {{ number_format($currentBalance, 0, ',', '.') }}</h3>
        </div>
        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-full dark:bg-indigo-500/10 dark:text-indigo-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </flux:card>

    <!-- Monthly Income -->
    <flux:card class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-zinc-500 mb-1">Pemasukan Bulan Ini</p>
            <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</h3>
        </div>
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-full dark:bg-emerald-500/10 dark:text-emerald-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </div>
    </flux:card>

    <!-- Monthly Expense -->
    <flux:card class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-zinc-500 mb-1">Pengeluaran Bulan Ini</p>
            <h3 class="text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</h3>
        </div>
        <div class="p-3 bg-red-50 text-red-600 rounded-full dark:bg-red-500/10 dark:text-red-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
        </div>
    </flux:card>
</div>
