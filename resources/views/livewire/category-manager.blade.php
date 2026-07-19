<?php

use Livewire\Volt\Component;
use App\Services\CategoryService;
use Livewire\Attributes\Layout;

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

    public function mount(string $type = 'income'): void
    {
        $this->type = in_array($type, ['income', 'expense']) ? $type : 'income';
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
                session()->flash('status', 'Kategori berhasil diperbarui.');
            }
        } else {
            $this->categoryService->create(auth()->id(), $this->type, [
                'name'  => $this->name,
                'color' => $this->color,
            ]);
            session()->flash('status', 'Kategori berhasil ditambahkan.');
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
            session()->flash('status', 'Kategori berhasil dihapus.', variant: 'danger');
        }

        $this->deleteModalOpen  = false;
        $this->categoryToDelete = null;
    }
}; ?>

<div>
    <x-slot name="header">
        <h1 class="text-xl font-bold tracking-tight">Pengaturan Kategori</h1>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Kategori {{ $type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                </h2>
            </div>

            <x-ui.button variant="default" icon="plus" wire:click="openModal">Tambah Kategori</x-ui.button>
        </div>

        <x-ui.card><x-ui.card-content class="pt-6">
            <x-ui.table>
                <x-ui.table-header><x-ui.table-row>
                    <x-ui.table-head>Nama Kategori</x-ui.table-head>
                    <x-ui.table-head>Tipe</x-ui.table-head>
                    <x-ui.table-head>Warna Label</x-ui.table-head>
                    <x-ui.table-head align="center">Aksi</x-ui.table-head>
                </x-ui.table-row></x-ui.table-header>
                
                <x-ui.table-body>
                    @forelse ($categories as $cat)
                        <x-ui.table-row>
                            <x-ui.table-cell class="font-medium">{{ $cat->name }}</x-ui.table-cell>
                            <x-ui.table-cell>
                                {{ $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </x-ui.table-cell>
                            <x-ui.table-cell>
                                <x-ui.badge class="{{ $cat->color }} text-white border-0">{{ $cat->name }}</x-ui.badge>
                            </x-ui.table-cell>
                            <x-ui.table-cell align="center">
                                <x-ui.dropdown width="48">
                                    <x-ui.button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                    
                                    <x-slot name="trigger"><button class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-muted"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button></x-slot><x-slot name="content">
                                        <x-ui.dropdown-item wire:click="editCategory({{ $cat->id }})" icon="pencil">
                                            Edit
                                        </x-ui.dropdown-item>
                                        <div class="h-px bg-muted my-1"></div>
                                        <x-ui.dropdown-item variant="destructive" wire:click="confirmDelete({{ $cat->id }})" icon="trash">
                                            Hapus
                                        </x-ui.dropdown-item>
                                    </x-slot>
                                </x-ui.dropdown>
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @empty
                        <x-ui.table-row>
                            <x-ui.table-cell colspan="4" class="text-center py-8 text-zinc-500">
                                Belum ada kategori untuk {{ $type === 'income' ? 'pemasukan' : 'pengeluaran' }}.
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @endforelse
                </x-ui.table-body>
            </x-ui.table>
        </x-ui.card-content></x-ui.card>
    </div>

    <!-- Modal Form -->
    <x-ui.modal name="create-category" wire:model="modalOpen" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">{{ $isEditMode ? 'Edit Kategori' : 'Tambah Kategori' }}</h1>
            </div>

            <div class="space-y-2">
                <x-ui.label>Nama Kategori</x-ui.label>
                <x-ui.input wire:model="name" placeholder="Contoh: Gaji / Makanan" />
                @error("name") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>
            
            <div class="space-y-2">
                <x-ui.label>Warna Label</x-ui.label>
                <select class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" wire:model="color">
                    <option value="bg-slate-500">Abu-abu</option>
                    <option value="bg-red-500">Merah</option>
                    <option value="bg-orange-500">Oranye</option>
                    <option value="bg-yellow-500">Kuning</option>
                    <option value="bg-emerald-500">Hijau</option>
                    <option value="bg-blue-500">Biru</option>
                    <option value="bg-indigo-500">Nila</option>
                    <option value="bg-purple-500">Ungu</option>
                    <option value="bg-pink-500">Merah Muda</option>
                </select>
            </div>

            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('modalOpen', false)">Batal</x-ui.button>
                <x-ui.button type="submit" variant="default">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Modal Konfirmasi Hapus -->
    <x-ui.modal name="delete-category" wire:model="deleteModalOpen" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Konfirmasi Hapus</h1>
                <p class="text-sm text-muted-foreground">Apakah Anda yakin ingin menghapus kategori ini? Transaksi terkait akan menjadi "Lain-lain".</p>
            </div>
            
            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('deleteModalOpen', false)">Batal</x-ui.button>
                <x-ui.button variant="destructive" wire:click="delete">Ya, Hapus</x-ui.button>
            </div>
        </div>
    </x-ui.modal>
</div>
