<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Transaction;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;

    public $type = 'expense';
    public $amount;
    public $description;
    public $transaction_date;

    public function mount()
    {
        $this->transaction_date = Carbon::now()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        Transaction::create([
            'user_id' => auth()->id(),
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'transaction_date' => $this->transaction_date,
        ]);

        $this->reset(['amount', 'description']);
        $this->transaction_date = Carbon::now()->format('Y-m-d');
        
        $this->dispatch('transaction-updated');
        session()->flash('message', 'Transaksi berhasil ditambahkan.');
    }

    public function delete($id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', auth()->id())->first();
        if ($transaction) {
            $transaction->delete();
            $this->dispatch('transaction-updated');
            session()->flash('message', 'Transaksi berhasil dihapus.');
        }
    }

    public function with(): array
    {
        return [
            'transactions' => Transaction::where('user_id', auth()->id())
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(10),
        ];
    }
}; ?>

<flux:card>
    <flux:heading size="lg" class="mb-6">Kelola Transaksi</flux:heading>
    
    @if (session()->has('message'))
        <div class="mb-6 bg-emerald-50 text-emerald-600 p-4 rounded-xl text-sm border border-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="save" class="mb-8 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <flux:field>
            <flux:label>Jenis</flux:label>
            <flux:select wire:model="type">
                <option value="expense">Pengeluaran</option>
                <option value="income">Pemasukan</option>
            </flux:select>
            <flux:error name="type" />
        </flux:field>

        <flux:field>
            <flux:label>Nominal (Rp)</flux:label>
            <flux:input type="number" wire:model="amount" placeholder="Contoh: 50000" />
            <flux:error name="amount" />
        </flux:field>

        <flux:field>
            <flux:label>Keterangan</flux:label>
            <flux:input type="text" wire:model="description" placeholder="Opsional" />
            <flux:error name="description" />
        </flux:field>

        <flux:field>
            <flux:label>Tanggal</flux:label>
            <flux:input type="date" wire:model="transaction_date" />
            <flux:error name="transaction_date" />
        </flux:field>

        <div class="pt-1 md:pt-0">
            <flux:button type="submit" variant="primary" class="w-full">
                Tambah Transaksi
            </flux:button>
        </div>
    </form>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Tanggal</flux:table.column>
            <flux:table.column>Jenis</flux:table.column>
            <flux:table.column>Keterangan</flux:table.column>
            <flux:table.column class="text-right">Nominal</flux:table.column>
            <flux:table.column align="center">Aksi</flux:table.column>
        </flux:table.columns>
        
        <flux:table.rows>
            @forelse ($transactions as $tx)
                <flux:table.row>
                    <flux:table.cell>{{ $tx->transaction_date->format('d M Y') }}</flux:table.cell>
                    <flux:table.cell>
                        @if($tx->type === 'income')
                            <flux:badge color="emerald">Pemasukan</flux:badge>
                        @else
                            <flux:badge color="red">Pengeluaran</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="text-zinc-500">{{ $tx->description ?? '-' }}</flux:table.cell>
                    <flux:table.cell class="text-right font-medium {{ $tx->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:button variant="danger" size="sm" icon="trash" wire:click="delete({{ $tx->id }})" wire:confirm="Yakin ingin menghapus transaksi ini?">
                            Hapus
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-8">
                        Belum ada transaksi.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</flux:card>
