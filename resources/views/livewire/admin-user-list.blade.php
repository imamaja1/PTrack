<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;
use Flux\Flux;

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
            Flux::toast('Anda tidak dapat menghapus akun Anda sendiri.', variant: 'danger');
            $this->modalOpen = false;
            return;
        }

        $user = User::find($id);
        if ($user) {
            $user->delete();
            Flux::toast('Pengguna berhasil dihapus.');
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
                Flux::toast('Anda tidak dapat mengubah peran (role) Anda sendiri.', variant: 'danger');
                return;
            }

            $user->name = $this->editName;
            $user->email = $this->editEmail;
            $user->role = $this->editRole;
            $user->save();

            Flux::toast('Data pengguna berhasil diperbarui.');
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

<flux:card>
    <flux:heading size="lg" class="mb-6">Daftar Pengguna</flux:heading>
    
    <flux:table>
        <flux:table.columns>
            <flux:table.column>ID</flux:table.column>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row>
                    <flux:table.cell>{{ $user->id }}</flux:table.cell>
                    <flux:table.cell>
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</span>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @if($user->role === 'admin')
                            <flux:badge class="bg-indigo-500 text-white border-0">Admin</flux:badge>
                        @else
                            <flux:badge class="bg-blue-500 text-white border-0">User</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            <flux:button variant="subtle" size="sm" wire:click="editUser({{ $user->id }})" icon="pencil">
                                Edit
                            </flux:button>

                            @if(auth()->id() !== $user->id)
                                <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $user->id }})" icon="trash">
                                    Hapus
                                </flux:button>
                            @else
                                <span class="text-zinc-400 text-xs italic self-center">Diri Anda</span>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-8">
                        Tidak ada pengguna ditemukan.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    
    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <flux:modal name="delete-user" wire:model="modalOpen" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:subheading>Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait (transaksi, dll) mungkin akan terhapus.</flux:subheading>
            </div>
            
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('modalOpen', false)">Batal</flux:button>
                <flux:button variant="danger" wire:click="delete">Ya, Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Edit User -->
    <flux:modal name="edit-user" wire:model="editModalOpen" class="md:w-[500px]">
        <form wire:submit="updateUser" class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Pengguna</flux:heading>
                <flux:subheading>Perbarui informasi akun pengguna.</flux:subheading>
            </div>
            
            <flux:field>
                <flux:label>Nama Lengkap</flux:label>
                <flux:input wire:model="editName" />
                <flux:error name="editName" />
            </flux:field>

            <flux:field>
                <flux:label>Alamat Email</flux:label>
                <flux:input type="email" wire:model="editEmail" />
                <flux:error name="editEmail" />
            </flux:field>

            <flux:field>
                <flux:label>Peran (Role)</flux:label>
                <flux:select wire:model="editRole">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </flux:select>
                <flux:error name="editRole" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button wire:click="$set('editModalOpen', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </flux:modal>
</flux:card>
