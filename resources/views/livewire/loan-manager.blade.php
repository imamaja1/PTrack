<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Services\LoanService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $borrower_name;
    public $amount;
    public $loan_date;
    public $expected_return_date;
    public $description;

    public $modalOpen = false;
    public $deleteModalOpen = false;
    public $loanToDelete = null;

    // For Edit
    public $editModalOpen = false;
    public $editLoanId = null;

    protected LoanService $loanService;

    public function boot(LoanService $loanService): void
    {
        $this->loanService = $loanService;
    }

    public function mount(): void
    {
        $this->loan_date = Carbon::now()->format('Y-m-d');
    }

    public function openModal(): void
    {
        $this->reset(['borrower_name', 'amount', 'expected_return_date', 'description']);
        $this->loan_date = Carbon::now()->format('Y-m-d');
        $this->modalOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'borrower_name'        => 'required|string|max:255',
            'amount'               => 'required|numeric|min:1',
            'loan_date'            => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:loan_date',
            'description'          => 'nullable|string|max:255',
        ]);

        $this->loanService->create(auth()->id(), 'lent', [
            'borrower_name'        => $this->borrower_name,
            'amount'               => $this->amount,
            'loan_date'            => $this->loan_date,
            'expected_return_date' => $this->expected_return_date,
            'description'          => $this->description,
        ]);

        $this->modalOpen = false;
        session()->flash('status', 'Pinjaman berhasil dicatat.');
    }

    public function toggleStatus(int $id): void
    {
        $this->loanService->toggleStatus($id, auth()->id(), 'lent');
        session()->flash('status', 'Status pinjaman berhasil diubah.');
    }

    public function editLoan(int $id): void
    {
        $loan = $this->loanService->findForUser($id, auth()->id(), 'lent');
        if ($loan) {
            $this->editLoanId             = $loan->id;
            $this->borrower_name          = $loan->borrower_name;
            $this->amount                 = $loan->amount;
            $this->loan_date              = $loan->loan_date->format('Y-m-d');
            $this->expected_return_date   = $loan->expected_return_date?->format('Y-m-d');
            $this->description            = $loan->description;
            $this->editModalOpen          = true;
        }
    }

    public function updateLoan(): void
    {
        $this->validate([
            'borrower_name'        => 'required|string|max:255',
            'amount'               => 'required|numeric|min:1',
            'loan_date'            => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:loan_date',
            'description'          => 'nullable|string|max:255',
        ]);

        $this->loanService->update($this->editLoanId, auth()->id(), 'lent', [
            'borrower_name'        => $this->borrower_name,
            'amount'               => $this->amount,
            'loan_date'            => $this->loan_date,
            'expected_return_date' => $this->expected_return_date,
            'description'          => $this->description,
        ]);

        session()->flash('status', 'Data pinjaman berhasil diperbarui.');
        $this->editModalOpen = false;
        $this->editLoanId    = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->loanToDelete  = $id;
        $this->deleteModalOpen = true;
    }

    public function delete(): void
    {
        if (!$this->loanToDelete) return;

        $this->loanService->delete($this->loanToDelete, auth()->id(), 'lent');
        session()->flash('status', 'Pinjaman berhasil dihapus.', variant: 'danger');

        $this->deleteModalOpen = false;
        $this->loanToDelete    = null;
    }

    public function with(): array
    {
        return [
            'totalUnpaid' => $this->loanService->getTotalUnpaid(auth()->id(), 'lent'),
            'loans'       => $this->loanService->getLoans(auth()->id(), 'lent'),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h1 class="text-xl font-bold tracking-tight">Pinjaman (Piutang)</h1>
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-end">
            <x-ui.button variant="default" icon="plus" wire:click="openModal">Catat Pinjaman Baru</x-ui.button>
        </div>
        <!-- Summary Card -->
        <x-ui.card class="bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-900"><x-ui.card-content class="pt-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-800 rounded-lg text-blue-600 dark:text-blue-300">
                    
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Total Uang Anda yang Dipinjam Orang (Belum Lunas)</h1>
                    <h2 class="text-2xl font-bold tracking-tight text-blue-600 dark:text-blue-400 mt-1">
                        Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
        </x-ui.card-content></x-ui.card>

        <x-ui.card><x-ui.card-content class="pt-6">
            <x-ui.table>
                <x-ui.table-header><x-ui.table-row>
                    <x-ui.table-head>Nama Peminjam</x-ui.table-head>
                    <x-ui.table-head>Tgl Pinjam</x-ui.table-head>
                    <x-ui.table-head>Jatuh Tempo</x-ui.table-head>
                    <x-ui.table-head class="text-right">Nominal</x-ui.table-head>
                    <x-ui.table-head align="center">Status</x-ui.table-head>
                    <x-ui.table-head align="center">Aksi</x-ui.table-head>
                </x-ui.table-row></x-ui.table-header>
                
                <x-ui.table-body>
                    @forelse ($loans as $loan)
                        <x-ui.table-row>
                            <x-ui.table-cell class="font-medium text-zinc-900 dark:text-white">{{ $loan->borrower_name }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-zinc-500">{{ $loan->loan_date->format('d M Y') }}</x-ui.table-cell>
                            <x-ui.table-cell>
                                @if($loan->expected_return_date)
                                    @php
                                        $isOverdue = $loan->status === 'unpaid' && $loan->expected_return_date->isPast();
                                    @endphp
                                    <span class="{{ $isOverdue ? 'text-red-500 font-semibold' : 'text-zinc-500' }}">
                                        {{ $loan->expected_return_date->format('d M Y') }}
                                        @if($isOverdue) <br><small>(Terlewat)</small> @endif
                                    </span>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </x-ui.table-cell>
                            <x-ui.table-cell class="text-right font-medium text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($loan->amount, 0, ',', '.') }}
                            </x-ui.table-cell>
                            <x-ui.table-cell align="center">
                                @if($loan->status === 'paid')
                                    <x-ui.badge class="bg-emerald-500 text-white border-0">Lunas</x-ui.badge>
                                @else
                                    <x-ui.badge class="bg-amber-500 text-white border-0">Belum Lunas</x-ui.badge>
                                @endif
                            </x-ui.table-cell>
                            <x-ui.table-cell align="center">
                                <x-ui.dropdown width="48">
                                    <x-ui.button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                    
                                    <x-slot name="trigger"><button class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-muted"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button></x-slot><x-slot name="content">
                                        <x-ui.dropdown-item wire:click="editLoan({{ $loan->id }})" icon="pencil">
                                            Edit
                                        </x-ui.dropdown-item>
                                        <x-ui.dropdown-item wire:click="toggleStatus({{ $loan->id }})" icon="{{ $loan->status === 'paid' ? 'x-circle' : 'check-circle' }}">
                                            Tandai {{ $loan->status === 'paid' ? 'Belum Lunas' : 'Lunas' }}
                                        </x-ui.dropdown-item>
                                        <div class="h-px bg-muted my-1"></div>
                                        <x-ui.dropdown-item variant="destructive" wire:click="confirmDelete({{ $loan->id }})" icon="trash">
                                            Hapus
                                        </x-ui.dropdown-item>
                                    </x-slot>
                                </x-ui.dropdown>
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @empty
                        <x-ui.table-row>
                            <x-ui.table-cell colspan="6" class="text-center py-8 text-zinc-500">
                                Belum ada data pinjaman.
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @endforelse
                </x-ui.table-body>
            </x-ui.table>
            
            <div class="mt-4">
                {{ $loans->links() }}
            </div>
        </x-ui.card-content></x-ui.card>
    </div>

    <!-- Modal Form -->
    <x-ui.modal name="create-loan" wire:model="modalOpen" class="md:w-[500px]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Catat Pinjaman Baru</h1>
                <p class="text-sm text-muted-foreground">Masukkan data orang yang meminjam uang Anda.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <div class="space-y-2">
                        <x-ui.label>Nama Peminjam</x-ui.label>
                        <x-ui.input type="text" wire:model="borrower_name" placeholder="Contoh: Budi" />
                        @error("borrower_name") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="space-y-2">
                        <x-ui.label>Nominal (Rp)</x-ui.label>
                        <x-ui.input type="number" wire:model="amount" placeholder="Contoh: 1000000" />
                        @error("amount") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="space-y-2">
                        <x-ui.label>Tgl Pinjam</x-ui.label>
                        <x-ui.input type="date" wire:model="loan_date" />
                        @error("loan_date") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="space-y-2">
                        <x-ui.label>Jatuh Tempo (Opsional)</x-ui.label>
                        <x-ui.input type="date" wire:model="expected_return_date" />
                        @error("expected_return_date") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('modalOpen', false)">Batal</x-ui.button>
                <x-ui.button type="submit" variant="default">Simpan Pinjaman</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Modal Konfirmasi Hapus -->
    <x-ui.modal name="delete-loan" wire:model="deleteModalOpen" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Konfirmasi Hapus</h1>
                <p class="text-sm text-muted-foreground">Apakah Anda yakin ingin menghapus data pinjaman ini?</p>
            </div>
            
            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('deleteModalOpen', false)">Batal</x-ui.button>
                <x-ui.button variant="destructive" wire:click="delete">Ya, Hapus</x-ui.button>
            </div>
        </div>
    </x-ui.modal>

    <!-- Modal Edit Pinjaman -->
    <x-ui.modal name="edit-loan" wire:model="editModalOpen" class="md:w-[500px]">
        <form wire:submit="updateLoan" class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Edit Pinjaman</h1>
                <p class="text-sm text-muted-foreground">Perbarui data catatan pinjaman.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <div class="space-y-2">
                        <x-ui.label>Nama Peminjam</x-ui.label>
                        <x-ui.input type="text" wire:model="borrower_name" />
                        @error("borrower_name") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="space-y-2">
                        <x-ui.label>Nominal (Rp)</x-ui.label>
                        <x-ui.input type="number" wire:model="amount" />
                        @error("amount") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="space-y-2">
                        <x-ui.label>Tgl Pinjam</x-ui.label>
                        <x-ui.input type="date" wire:model="loan_date" />
                        @error("loan_date") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="space-y-2">
                        <x-ui.label>Jatuh Tempo (Opsional)</x-ui.label>
                        <x-ui.input type="date" wire:model="expected_return_date" />
                        @error("expected_return_date") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <x-ui.label>Catatan</x-ui.label>
                <x-ui.input type="text" wire:model="description" />
                @error("description") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('editModalOpen', false)">Batal</x-ui.button>
                <x-ui.button type="submit" variant="default">Simpan Perubahan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
