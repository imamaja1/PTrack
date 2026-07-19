<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Services\TransactionService;
use App\Services\CategoryService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Flux\Flux;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $amount;
    public $description;
    public $transaction_date;
    public $category_id;

    // For Modals
    public $modalOpen = false;
    public $newCategoryModal = false;
    public $newCategoryName = '';
    public $newCategoryColor = 'bg-red-500';
    public $deleteModalOpen = false;
    public $transactionToDelete = null;

    // For Edit
    public $editModalOpen = false;
    public $editTransactionId = null;

    protected TransactionService $transactionService;
    protected CategoryService $categoryService;

    public function boot(TransactionService $transactionService, CategoryService $categoryService): void
    {
        $this->transactionService = $transactionService;
        $this->categoryService = $categoryService;
    }

    public function mount(): void
    {
        $this->transaction_date = Carbon::now()->format('Y-m-d');
        $defaultCat = $this->categoryService->getByType(auth()->id(), 'expense')->first();
        if ($defaultCat) {
            $this->category_id = $defaultCat->id;
        }
    }

    public function openModal(): void
    {
        $this->reset(['amount', 'description']);
        $this->transaction_date = Carbon::now()->format('Y-m-d');
        $this->modalOpen = true;
    }

    public function saveCategory(): void
    {
        $this->validate([
            'newCategoryName'  => 'required|string|max:50',
            'newCategoryColor' => 'required|string|max:20',
        ]);

        $cat = $this->categoryService->create(auth()->id(), 'expense', [
            'name'  => $this->newCategoryName,
            'color' => $this->newCategoryColor,
        ]);

        $this->category_id = $cat->id;
        $this->reset(['newCategoryName', 'newCategoryModal']);
        Flux::toast('Kategori berhasil ditambahkan.');
    }

    public function save(): void
    {
        $this->validate([
            'amount'           => 'required|numeric|min:1',
            'category_id'      => 'required|exists:categories,id',
            'description'      => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        $this->transactionService->createExpense(auth()->id(), [
            'amount'           => $this->amount,
            'category_id'      => $this->category_id,
            'description'      => $this->description,
            'transaction_date' => $this->transaction_date,
        ]);

        $this->modalOpen = false;
        $this->dispatch('transaction-updated');
        Flux::toast('Pengeluaran berhasil dicatat.');
    }

    public function editTransaction(int $id): void
    {
        $transaction = $this->transactionService->findForUser($id, auth()->id());
        if ($transaction) {
            $this->editTransactionId = $transaction->id;
            $this->amount            = $transaction->amount;
            $this->category_id       = $transaction->category_id;
            $this->description       = $transaction->description;
            $this->transaction_date  = $transaction->transaction_date->format('Y-m-d');
            $this->editModalOpen     = true;
        }
    }

    public function update(): void
    {
        $this->validate([
            'amount'           => 'required|numeric|min:1',
            'category_id'      => 'required|exists:categories,id',
            'description'      => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        $updated = $this->transactionService->update($this->editTransactionId, auth()->id(), [
            'amount'           => $this->amount,
            'category_id'      => $this->category_id,
            'description'      => $this->description,
            'transaction_date' => $this->transaction_date,
        ]);

        if ($updated) {
            $this->dispatch('transaction-updated');
            Flux::toast('Pengeluaran berhasil diperbarui.');
        }

        $this->editModalOpen     = false;
        $this->editTransactionId = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->transactionToDelete = $id;
        $this->deleteModalOpen = true;
    }

    public function delete(): void
    {
        if (!$this->transactionToDelete) return;

        $deleted = $this->transactionService->delete($this->transactionToDelete, auth()->id());
        if ($deleted) {
            $this->dispatch('transaction-updated');
            Flux::toast('Pengeluaran berhasil dihapus.', variant: 'danger');
        }

        $this->deleteModalOpen     = false;
        $this->transactionToDelete = null;
    }

    public function with(): array
    {
        return [
            'transactions' => $this->transactionService->getExpense(auth()->id()),
            'categories'   => $this->categoryService->getByType(auth()->id(), 'expense'),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <flux:heading size="xl" level="1">Pengeluaran</flux:heading>
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-end">
            <flux:button variant="danger" icon="plus" wire:click="openModal">Catat Pengeluaran</flux:button>
        </div>
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Tanggal</flux:table.column>
                    <flux:table.column>Kategori</flux:table.column>
                    <flux:table.column>Keterangan</flux:table.column>
                    <flux:table.column class="text-right">Nominal</flux:table.column>
                    <flux:table.column align="center">Aksi</flux:table.column>
                </flux:table.columns>
                
                <flux:table.rows>
                    @forelse ($transactions as $tx)
                        <flux:table.row>
                            <flux:table.cell>{{ $tx->transaction_date->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell>
                                @if($tx->category)
                                    <flux:badge class="{{ $tx->category->color }} text-white border-0">{{ $tx->category->name }}</flux:badge>
                                @else
                                    <flux:badge>Lain-lain</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-500">{{ $tx->description ?? '-' }}</flux:table.cell>
                            <flux:table.cell class="text-right font-medium text-red-600 dark:text-red-400">
                                - Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <div class="flex gap-2 justify-center items-center">
                                    <button
                                        wire:click="editTransaction({{ $tx->id }})"
                                        title="Edit Pengeluaran"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-900/30 transition-colors duration-150"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="confirmDelete({{ $tx->id }})"
                                        title="Hapus Pengeluaran"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 transition-colors duration-150"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">
                                Belum ada pengeluaran.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </flux:card>
    </div>

    <!-- Modal Pengeluaran -->
    <flux:modal name="create-expense" wire:model="modalOpen" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">Catat Pengeluaran</flux:heading>
            </div>

            <flux:field>
                <flux:label>Nominal (Rp)</flux:label>
                <flux:input type="number" wire:model="amount" placeholder="Contoh: 150000" />
                <flux:error name="amount" />
            </flux:field>

            <div class="relative">
                <flux:field>
                    <flux:label>Kategori</flux:label>
                    <flux:select wire:model="category_id">
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>
                <button type="button" wire:click="$set('newCategoryModal', true)" class="absolute -top-1 right-0 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                    + Kategori Baru
                </button>
            </div>

            <flux:field>
                <flux:label>Keterangan</flux:label>
                <flux:input type="text" wire:model="description" placeholder="Opsional (Bensin, Makan, dll)" />
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:label>Tanggal</flux:label>
                <flux:input type="date" wire:model="transaction_date" />
                <flux:error name="transaction_date" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('modalOpen', false)">Batal</flux:button>
                <flux:button type="submit" variant="danger">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Kategori Baru -->
    <flux:modal name="create-category-expense" wire:model="newCategoryModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Kategori Baru</flux:heading>
            </div>
            <flux:field>
                <flux:label>Nama Kategori</flux:label>
                <flux:input wire:model="newCategoryName" />
            </flux:field>
            <flux:field>
                <flux:label>Warna Label</flux:label>
                <flux:select wire:model="newCategoryColor">
                    <option value="bg-red-500">Merah</option>
                    <option value="bg-orange-500">Oranye</option>
                    <option value="bg-rose-500">Mawar (Rose)</option>
                    <option value="bg-fuchsia-500">Fuchsia</option>
                </flux:select>
            </flux:field>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('newCategoryModal', false)">Batal</flux:button>
                <flux:button variant="danger" wire:click="saveCategory">Simpan Kategori</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Konfirmasi Hapus -->
    <flux:modal name="delete-expense" wire:model="deleteModalOpen" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:subheading>Apakah Anda yakin ingin menghapus catatan pengeluaran ini?</flux:subheading>
            </div>
            
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('deleteModalOpen', false)">Batal</flux:button>
                <flux:button variant="danger" wire:click="delete">Ya, Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Edit Pengeluaran -->
    <flux:modal name="edit-expense" wire:model="editModalOpen" class="md:w-96">
        <form wire:submit="update" class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Pengeluaran</flux:heading>
                <flux:subheading>Perbarui data catatan pengeluaran.</flux:subheading>
            </div>

            <flux:field>
                <flux:label>Nominal (Rp)</flux:label>
                <flux:input type="number" wire:model="amount" />
                <flux:error name="amount" />
            </flux:field>

            <flux:field>
                <flux:label>Kategori</flux:label>
                <flux:select wire:model="category_id">
                    <option value="">Pilih Kategori...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="category_id" />
            </flux:field>

            <flux:field>
                <flux:label>Keterangan</flux:label>
                <flux:input type="text" wire:model="description" />
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:label>Tanggal</flux:label>
                <flux:input type="date" wire:model="transaction_date" />
                <flux:error name="transaction_date" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('editModalOpen', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
