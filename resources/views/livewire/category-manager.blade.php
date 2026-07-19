<?php

use Livewire\Volt\Component;
use App\Services\CategoryService;
use Livewire\Attributes\Layout;
use Flux\Flux;

new #[Layout('layouts.app')] class extends Component {
    public $categories = [];
    public $type = 'income'; // default tab

    // Form
    public $categoryId = null;
    public $name = '';
    public $color = 'bg-blue-500';
    public $isEditMode = false;
    public $modalOpen = false;
    public $deleteModalOpen = false;
    public $categoryToDelete = null;

    protected CategoryService $categoryService;

    public function boot(CategoryService $categoryService): void
    {
        $this->categoryService = $categoryService;
    }

    public function mount(): void
    {
        $this->loadCategories();
    }

    public function setType(string $type): void
    {
        $this->type = $type;
        $this->loadCategories();
    }

    public function loadCategories(): void
    {
        $this->categories = $this->categoryService->getByType(auth()->id(), $this->type);
    }

    public function openModal(): void
    {
        $this->reset(['categoryId', 'name', 'isEditMode']);
        $this->color = $this->type === 'income' ? 'bg-emerald-500' : 'bg-red-500';
        $this->modalOpen = true;
    }

    public function editCategory(int $id): void
    {
        $cat = $this->categoryService->findForUser($id, auth()->id());
        if ($cat) {
            $this->categoryId = $cat->id;
            $this->name       = $cat->name;
            $this->color      = $cat->color;
            $this->isEditMode = true;
            $this->modalOpen  = true;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name'  => 'required|string|max:50',
            'color' => 'required|string|max:20',
        ]);

        if ($this->isEditMode && $this->categoryId) {
            $updated = $this->categoryService->update($this->categoryId, auth()->id(), [
                'name'  => $this->name,
                'color' => $this->color,
            ]);
            if ($updated) {
                Flux::toast('Kategori berhasil diperbarui.');
            }
        } else {
            $this->categoryService->create(auth()->id(), $this->type, [
                'name'  => $this->name,
                'color' => $this->color,
            ]);
            Flux::toast('Kategori berhasil ditambahkan.');
        }

        $this->modalOpen = false;
        $this->loadCategories();
    }

    public function confirmDelete(int $id): void
    {
        $this->categoryToDelete = $id;
        $this->deleteModalOpen  = true;
    }

    public function delete(): void
    {
        if (!$this->categoryToDelete) return;

        $deleted = $this->categoryService->delete($this->categoryToDelete, auth()->id());
        if ($deleted) {
            $this->loadCategories();
            Flux::toast('Kategori berhasil dihapus.', variant: 'danger');
        }

        $this->deleteModalOpen  = false;
        $this->categoryToDelete = null;
    }
}; ?>

<div>
    <x-slot name="header">
        <flux:heading size="xl" level="1">Pengaturan Kategori</flux:heading>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <flux:radio.group wire:model.live="type" wire:change="setType($event.target.value)" variant="segmented">
                <flux:radio value="income" label="Pemasukan" />
                <flux:radio value="expense" label="Pengeluaran" />
            </flux:radio.group>

            <flux:button variant="primary" icon="plus" wire:click="openModal">Tambah Kategori</flux:button>
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nama Kategori</flux:table.column>
                    <flux:table.column>Tipe</flux:table.column>
                    <flux:table.column>Warna Label</flux:table.column>
                    <flux:table.column align="center">Aksi</flux:table.column>
                </flux:table.columns>
                
                <flux:table.rows>
                    @forelse ($categories as $cat)
                        <flux:table.row>
                            <flux:table.cell class="font-medium">{{ $cat->name }}</flux:table.cell>
                            <flux:table.cell>
                                {{ $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge class="{{ $cat->color }} text-white border-0">{{ $cat->name }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                    
                                    <flux:menu>
                                        <flux:menu.item wire:click="editCategory({{ $cat->id }})" icon="pencil">
                                            Edit
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item variant="danger" wire:click="confirmDelete({{ $cat->id }})" icon="trash">
                                            Hapus
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="text-center py-8 text-zinc-500">
                                Belum ada kategori untuk {{ $type === 'income' ? 'pemasukan' : 'pengeluaran' }}.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>

    <!-- Modal Form -->
    <flux:modal name="create-category" wire:model="modalOpen" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $isEditMode ? 'Edit Kategori' : 'Tambah Kategori' }}</flux:heading>
            </div>

            <flux:field>
                <flux:label>Nama Kategori</flux:label>
                <flux:input wire:model="name" placeholder="Contoh: Gaji / Makanan" />
                <flux:error name="name" />
            </flux:field>
            
            <flux:field>
                <flux:label>Warna Label</flux:label>
                <flux:select wire:model="color">
                    <option value="bg-slate-500">Abu-abu</option>
                    <option value="bg-red-500">Merah</option>
                    <option value="bg-orange-500">Oranye</option>
                    <option value="bg-yellow-500">Kuning</option>
                    <option value="bg-emerald-500">Hijau</option>
                    <option value="bg-blue-500">Biru</option>
                    <option value="bg-indigo-500">Nila</option>
                    <option value="bg-purple-500">Ungu</option>
                    <option value="bg-pink-500">Merah Muda</option>
                </flux:select>
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('modalOpen', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Konfirmasi Hapus -->
    <flux:modal name="delete-category" wire:model="deleteModalOpen" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:subheading>Apakah Anda yakin ingin menghapus kategori ini? Transaksi terkait akan menjadi "Lain-lain".</flux:subheading>
            </div>
            
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('deleteModalOpen', false)">Batal</flux:button>
                <flux:button variant="danger" wire:click="delete">Ya, Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
