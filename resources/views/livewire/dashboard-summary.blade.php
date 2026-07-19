<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Services\TransactionService;

new class extends Component {
    public $currentBalance = 0;
    public $monthlyIncome = 0;
    public $monthlyExpense = 0;

    protected TransactionService $transactionService;

    public function boot(TransactionService $transactionService): void
    {
        $this->transactionService = $transactionService;
    }

    public function mount(): void
    {
        $this->loadSummary();
    }

    #[On('transaction-updated')]
    public function loadSummary(): void
    {
        $summary = $this->transactionService->getSummary(auth()->id());

        $this->currentBalance = $summary['currentBalance'];
        $this->monthlyIncome  = $summary['monthlyIncome'];
        $this->monthlyExpense = $summary['monthlyExpense'];
    }
}; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Current Balance -->
    <x-ui.card class="p-6 flex items-center justify-between h-full">
        <div>
            <p class="text-sm font-medium text-zinc-500 mb-1">Saldo Saat Ini</p>
            <h3 class="text-2xl font-bold text-zinc-900 dark:text-white">Rp {{ number_format($currentBalance, 0, ',', '.') }}</h3>
        </div>
        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-full dark:bg-indigo-500/10 dark:text-indigo-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </x-ui.card>

    <!-- Monthly Income -->
    <x-ui.card class="p-6 flex items-center justify-between h-full">
        <div>
            <p class="text-sm font-medium text-zinc-500 mb-1">Pemasukan Bulan Ini</p>
            <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</h3>
        </div>
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-full dark:bg-emerald-500/10 dark:text-emerald-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </div>
    </x-ui.card>

    <!-- Monthly Expense -->
    <x-ui.card class="p-6 flex items-center justify-between h-full">
        <div>
            <p class="text-sm font-medium text-zinc-500 mb-1">Pengeluaran Bulan Ini</p>
            <h3 class="text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</h3>
        </div>
        <div class="p-3 bg-red-50 text-red-600 rounded-full dark:bg-red-500/10 dark:text-red-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
        </div>
    </x-ui.card>
</div>
