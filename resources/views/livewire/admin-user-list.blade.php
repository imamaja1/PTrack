<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;

new class extends Component {
    use WithPagination;

    public $userToDelete = null;
    public $modalOpen = false;

    public $editModalOpen = false;
    public $editUserId = null;
    public $editName = '';
    public $editEmail = '';
    public $editRole = 'user';

    public function confirmDelete($id)
    {
        $this->userToDelete = $id;
        $this->modalOpen = true;
    }

    public function delete()
    {
        $id = $this->userToDelete;
        if (!$id) return;

        // Prevent deleting oneself
        if (auth()->id() == $id) {
            session()->flash('status', 'Anda tidak dapat menghapus akun Anda sendiri.', variant: 'danger');
            $this->modalOpen = false;
            return;
        }

        $user = User::find($id);
        if ($user) {
            $user->delete();
            session()->flash('status', 'Pengguna berhasil dihapus.');
        }

        $this->modalOpen = false;
        $this->userToDelete = null;
    }

    public function editUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->editUserId = $user->id;
            $this->editName = $user->name;
            $this->editEmail = $user->email;
            $this->editRole = $user->role ?? 'user'; // Provide default if null
            $this->editModalOpen = true;
        }
    }

    public function updateUser()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $this->editUserId,
            'editRole' => 'required|in:admin,user',
        ]);

        $user = User::find($this->editUserId);
        if ($user) {
            // Prevent removing own admin role
            if (auth()->id() == $user->id && $this->editRole !== 'admin') {
                session()->flash('status', 'Anda tidak dapat mengubah peran (role) Anda sendiri.', variant: 'danger');
                return;
            }

            $user->name = $this->editName;
            $user->email = $this->editEmail;
            $user->role = $this->editRole;
            $user->save();

            session()->flash('status', 'Data pengguna berhasil diperbarui.');
            $this->editModalOpen = false;
        }
    }

    public function with(): array
    {
        return [
            'users' => User::orderBy('created_at', 'desc')->paginate(10),
        ];
    }
}; ?>

<x-ui.card><x-ui.card-content class="pt-6">
    <h1 class="text-xl font-bold tracking-tight">Daftar Pengguna</h1>
    
    <x-ui.table>
        <x-ui.table-header><x-ui.table-row>
            <x-ui.table-head>ID</x-ui.table-head>
            <x-ui.table-head>Nama</x-ui.table-head>
            <x-ui.table-head>Email</x-ui.table-head>
            <x-ui.table-head>Role</x-ui.table-head>
            <x-ui.table-head>Aksi</x-ui.table-head>
        </x-ui.table-row></x-ui.table-header>

        <x-ui.table-body>
            @forelse ($users as $user)
                <x-ui.table-row>
                    <x-ui.table-cell>{{ $user->id }}</x-ui.table-cell>
                    <x-ui.table-cell>
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</span>
                    </x-ui.table-cell>
                    <x-ui.table-cell>{{ $user->email }}</x-ui.table-cell>
                    <x-ui.table-cell>
                        @if($user->role === 'admin')
                            <x-ui.badge class="bg-indigo-500 text-white border-0">Admin</x-ui.badge>
                        @else
                            <x-ui.badge class="bg-blue-500 text-white border-0">User</x-ui.badge>
                        @endif
                    </x-ui.table-cell>
                    <x-ui.table-cell>
                        <div class="flex gap-2">
                            <x-ui.button variant="subtle" size="sm" wire:click="editUser({{ $user->id }})" icon="pencil">
                                Edit
                            </x-ui.button>

                            @if(auth()->id() !== $user->id)
                                <x-ui.button variant="destructive" size="sm" wire:click="confirmDelete({{ $user->id }})" icon="trash">
                                    Hapus
                                </x-ui.button>
                            @else
                                <span class="text-zinc-400 text-xs italic self-center">Diri Anda</span>
                            @endif
                        </div>
                    </x-ui.table-cell>
                </x-ui.table-row>
            @empty
                <x-ui.table-row>
                    <x-ui.table-cell colspan="5" class="text-center py-8">
                        Tidak ada pengguna ditemukan.
                    </x-ui.table-cell>
                </x-ui.table-row>
            @endforelse
        </x-ui.table-body>
    </x-ui.table>
    
    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <x-ui.modal name="delete-user" wire:model="modalOpen" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Konfirmasi Hapus</h1>
                <p class="text-sm text-muted-foreground">Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait (transaksi, dll) mungkin akan terhapus.</p>
            </div>
            
            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('modalOpen', false)">Batal</x-ui.button>
                <x-ui.button variant="destructive" wire:click="delete">Ya, Hapus</x-ui.button>
            </div>
        </div>
    </x-ui.modal>

    <!-- Modal Edit User -->
    <x-ui.modal name="edit-user" wire:model="editModalOpen" class="md:w-[500px]">
        <form wire:submit="updateUser" class="space-y-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Edit Pengguna</h1>
                <p class="text-sm text-muted-foreground">Perbarui informasi akun pengguna.</p>
            </div>
            
            <div class="space-y-2">
                <x-ui.label>Nama Lengkap</x-ui.label>
                <x-ui.input wire:model="editName" />
                @error("editName") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <x-ui.label>Alamat Email</x-ui.label>
                <x-ui.input type="email" wire:model="editEmail" />
                @error("editEmail") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <x-ui.label>Peran (Role)</x-ui.label>
                <select class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" wire:model="editRole">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                @error("editRole") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2">
                <div class="flex-1"></div>
                <x-ui.button wire:click="$set('editModalOpen', false)">Batal</x-ui.button>
                <x-ui.button type="submit" variant="default">Simpan Perubahan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</x-ui.card-content></x-ui.card>
