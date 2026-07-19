<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Services\TransactionService;
use App\Services\CategoryService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

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
        session()->flash('status', 'Kategori berhasil ditambahkan.');
    }

    public function save(): void
    {
        $this->validate([
            'amount'           => 'required|numeric|min:1',
'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
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
        session()->flash('status', 'Pengeluaran berhasil dicatat.');
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
'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
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
            session()->flash('status', 'Pengeluaran berhasil diperbarui.');
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
            session()->flash('status', 'Pengeluaran berhasil dihapus.', variant: 'danger');
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
        <h1 class="text-xl font-bold tracking-tight">Pengeluaran</h1>
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-end">
            <x-ui.button variant="destructive" icon="plus" wire:click="openModal">Catat Pengeluaran</x-ui.button>
        </div>
        <x-ui.card><x-ui.card-content class="pt-6">
            <x-ui.table>
                <x-ui.table-header><x-ui.table-row>
                    <x-ui.table-head>Tanggal</x-ui.table-head>
                    <x-ui.table-head>Kategori</x-ui.table-head>
                    <x-ui.table-head>Keterangan</x-ui.table-head>
                    <x-ui.table-head class="text-right">Nominal</x-ui.table-head>
                    <x-ui.table-head align="center">Aksi</x-ui.table-head>
                </x-ui.table-row></x-ui.table-header>
                
                <x-ui.table-body>
                    @forelse ($transactions as $tx)
                        <x-ui.table-row>
                            <x-ui.table-cell>{{ $tx->transaction_date->format('d M Y') }}</x-ui.table-cell>
                            <x-ui.table-cell>
                                @if($tx->category)
                                    <x-ui.badge class="{{ $tx->category->color }} text-white border-0">{{ $tx->category->name }}</x-ui.badge>
                                @else
                                    <x-ui.badge>Lain-lain</x-ui.badge>
                                @endif
                            </x-ui.table-cell>
                            <x-ui.table-cell class="text-zinc-500">{{ $tx->description ?? '-' }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-right font-medium text-red-600 dark:text-red-400">
                                - Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </x-ui.table-cell>
                            <x-ui.table-cell align="center">
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
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @empty
                        <x-ui.table-row>
                            <x-ui.table-cell colspan="5" class="text-center py-8 text-zinc-500">
                                Belum ada pengeluaran.
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @endforelse
                </x-ui.table-body>
            </x-ui.table>
            
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </x-ui.card-content></x-ui.card>
    </div>

    <!-- Modal Pengeluaran -->
    <x-ui.modal name="create-expense" wire:model="modalOpen" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Catat Pengeluaran</h1>
            </div>

            <div class="space-y-2">
                <x-ui.label>Nominal (Rp)</x-ui.label>
                <x-ui.input type="number" wire:model="amount" placeholder="Contoh: 150000" />
                @error("amount") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="relative">
                <div class="space-y-2">
                    <x-ui.label>Kategori</x-ui.label>
                    <select class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" wire:model="category_id">
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error("category_id") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
                </div>
                <button type="button" wire:click="$set('newCategoryModal', true)" class="absolute -top-1 right-0 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                    + Kategori Baru
                </button>
            </div>

            <div class="space-y-2">
                <x-ui.label>Keterangan</x-ui.label>
                <x-ui.input type="text" wire:model="description" placeholder="Opsional (Bensin, Makan, dll)" />
                @error("description") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <x-ui.label>Tanggal</x-ui.label>
                <x-ui.input type="date" wire:model="transaction_date" />
                @error("transaction_date") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('modalOpen', false)">Batal</x-ui.button>
                <x-ui.button type="submit" variant="destructive">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Modal Kategori Baru -->
    <x-ui.modal name="create-category-expense" wire:model="newCategoryModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Tambah Kategori Baru</h1>
            </div>
            <div class="space-y-2">
                <x-ui.label>Nama Kategori</x-ui.label>
                <x-ui.input wire:model="newCategoryName" />
            </div>
            <div class="space-y-2">
                <x-ui.label>Warna Label</x-ui.label>
                <select class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" wire:model="newCategoryColor">
                    <option value="bg-red-500">Merah</option>
                    <option value="bg-orange-500">Oranye</option>
                    <option value="bg-rose-500">Mawar (Rose)</option>
                    <option value="bg-fuchsia-500">Fuchsia</option>
                </select>
            </div>
            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('newCategoryModal', false)">Batal</x-ui.button>
                <x-ui.button variant="destructive" wire:click="saveCategory">Simpan Kategori</x-ui.button>
            </div>
        </div>
    </x-ui.modal>

    <!-- Modal Konfirmasi Hapus -->
    <x-ui.modal name="delete-expense" wire:model="deleteModalOpen" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Konfirmasi Hapus</h1>
                <p class="text-sm text-muted-foreground">Apakah Anda yakin ingin menghapus catatan pengeluaran ini?</p>
            </div>
            
            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('deleteModalOpen', false)">Batal</x-ui.button>
                <x-ui.button variant="destructive" wire:click="delete">Ya, Hapus</x-ui.button>
            </div>
        </div>
    </x-ui.modal>

    <!-- Modal Edit Pengeluaran -->
    <x-ui.modal name="edit-expense" wire:model="editModalOpen" class="md:w-96">
        <form wire:submit="update" class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Edit Pengeluaran</h1>
                <p class="text-sm text-muted-foreground">Perbarui data catatan pengeluaran.</p>
            </div>

            <div class="space-y-2">
                <x-ui.label>Nominal (Rp)</x-ui.label>
                <x-ui.input type="number" wire:model="amount" />
                @error("amount") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <x-ui.label>Kategori</x-ui.label>
                <select class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" wire:model="category_id">
                    <option value="">Pilih Kategori...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error("category_id") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <x-ui.label>Keterangan</x-ui.label>
                <x-ui.input type="text" wire:model="description" />
                @error("description") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <x-ui.label>Tanggal</x-ui.label>
                <x-ui.input type="date" wire:model="transaction_date" />
                @error("transaction_date") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('editModalOpen', false)">Batal</x-ui.button>
                <x-ui.button type="submit" variant="default">Simpan Perubahan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
