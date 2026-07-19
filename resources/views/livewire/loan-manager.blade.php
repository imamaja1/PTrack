<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Services\LoanService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Flux\Flux;

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
        Flux::toast('Pinjaman berhasil dicatat.');
    }

    public function toggleStatus(int $id): void
    {
        $this->loanService->toggleStatus($id, auth()->id(), 'lent');
        Flux::toast('Status pinjaman berhasil diubah.');
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

        Flux::toast('Data pinjaman berhasil diperbarui.');
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
        Flux::toast('Pinjaman berhasil dihapus.', variant: 'danger');

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
        <flux:heading size="xl" level="1">Pinjaman (Piutang)</flux:heading>
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-end">
            <flux:button variant="primary" icon="plus" wire:click="openModal">Catat Pinjaman Baru</flux:button>
        </div>
        <!-- Summary Card -->
        <flux:card class="bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-900">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-800 rounded-lg text-blue-600 dark:text-blue-300">
                    <flux:icon.users variant="solid" />
                </div>
                <div>
                    <flux:heading size="md" class="text-blue-900 dark:text-blue-100">Total Uang Anda yang Dipinjam Orang (Belum Lunas)</flux:heading>
                    <flux:heading size="2xl" class="text-blue-600 dark:text-blue-400 mt-1">
                        Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                    </flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nama Peminjam</flux:table.column>
                    <flux:table.column>Tgl Pinjam</flux:table.column>
                    <flux:table.column>Jatuh Tempo</flux:table.column>
                    <flux:table.column class="text-right">Nominal</flux:table.column>
                    <flux:table.column align="center">Status</flux:table.column>
                    <flux:table.column align="center">Aksi</flux:table.column>
                </flux:table.columns>
                
                <flux:table.rows>
                    @forelse ($loans as $loan)
                        <flux:table.row>
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $loan->borrower_name }}</flux:table.cell>
                            <flux:table.cell class="text-zinc-500">{{ $loan->loan_date->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell>
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
                            </flux:table.cell>
                            <flux:table.cell class="text-right font-medium text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($loan->amount, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if($loan->status === 'paid')
                                    <flux:badge class="bg-emerald-500 text-white border-0">Lunas</flux:badge>
                                @else
                                    <flux:badge class="bg-amber-500 text-white border-0">Belum Lunas</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                    
                                    <flux:menu>
                                        <flux:menu.item wire:click="editLoan({{ $loan->id }})" icon="pencil">
                                            Edit
                                        </flux:menu.item>
                                        <flux:menu.item wire:click="toggleStatus({{ $loan->id }})" icon="{{ $loan->status === 'paid' ? 'x-circle' : 'check-circle' }}">
                                            Tandai {{ $loan->status === 'paid' ? 'Belum Lunas' : 'Lunas' }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item variant="danger" wire:click="confirmDelete({{ $loan->id }})" icon="trash">
                                            Hapus
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">
                                Belum ada data pinjaman.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            
            <div class="mt-4">
                {{ $loans->links() }}
            </div>
        </flux:card>
    </div>

    <!-- Modal Form -->
    <flux:modal name="create-loan" wire:model="modalOpen" class="md:w-[500px]">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">Catat Pinjaman Baru</flux:heading>
                <flux:subheading>Masukkan data orang yang meminjam uang Anda.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <flux:field>
                        <flux:label>Nama Peminjam</flux:label>
                        <flux:input type="text" wire:model="borrower_name" placeholder="Contoh: Budi" />
                        <flux:error name="borrower_name" />
                    </flux:field>
                </div>

                <div class="col-span-2">
                    <flux:field>
                        <flux:label>Nominal (Rp)</flux:label>
                        <flux:input type="number" wire:model="amount" placeholder="Contoh: 1000000" />
                        <flux:error name="amount" />
                    </flux:field>
                </div>

                <div class="col-span-1">
                    <flux:field>
                        <flux:label>Tgl Pinjam</flux:label>
                        <flux:input type="date" wire:model="loan_date" />
                        <flux:error name="loan_date" />
                    </flux:field>
                </div>

                <div class="col-span-1">
                    <flux:field>
                        <flux:label>Jatuh Tempo (Opsional)</flux:label>
                        <flux:input type="date" wire:model="expected_return_date" />
                        <flux:error name="expected_return_date" />
                    </flux:field>
                </div>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('modalOpen', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Pinjaman</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Konfirmasi Hapus -->
    <flux:modal name="delete-loan" wire:model="deleteModalOpen" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:subheading>Apakah Anda yakin ingin menghapus data pinjaman ini?</flux:subheading>
            </div>
            
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('deleteModalOpen', false)">Batal</flux:button>
                <flux:button variant="danger" wire:click="delete">Ya, Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Edit Pinjaman -->
    <flux:modal name="edit-loan" wire:model="editModalOpen" class="md:w-[500px]">
        <form wire:submit="updateLoan" class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Pinjaman</flux:heading>
                <flux:subheading>Perbarui data catatan pinjaman.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <flux:field>
                        <flux:label>Nama Peminjam</flux:label>
                        <flux:input type="text" wire:model="borrower_name" />
                        <flux:error name="borrower_name" />
                    </flux:field>
                </div>

                <div class="col-span-2">
                    <flux:field>
                        <flux:label>Nominal (Rp)</flux:label>
                        <flux:input type="number" wire:model="amount" />
                        <flux:error name="amount" />
                    </flux:field>
                </div>

                <div class="col-span-1">
                    <flux:field>
                        <flux:label>Tgl Pinjam</flux:label>
                        <flux:input type="date" wire:model="loan_date" />
                        <flux:error name="loan_date" />
                    </flux:field>
                </div>

                <div class="col-span-1">
                    <flux:field>
                        <flux:label>Jatuh Tempo (Opsional)</flux:label>
                        <flux:input type="date" wire:model="expected_return_date" />
                        <flux:error name="expected_return_date" />
                    </flux:field>
                </div>
            </div>

            <flux:field>
                <flux:label>Catatan</flux:label>
                <flux:input type="text" wire:model="description" />
                <flux:error name="description" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('editModalOpen', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
