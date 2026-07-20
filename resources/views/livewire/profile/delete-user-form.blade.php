<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';
    public bool $confirmingUserDeletion = false;

    public function confirmUserDeletion(): void
    {
        $this->confirmingUserDeletion = true;
        $this->resetErrorBag();
        $this->password = '';
    }

    public function closeModal(): void
    {
        $this->confirmingUserDeletion = false;
        $this->resetErrorBag();
        $this->password = '';
    }

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <p class="text-sm text-slate-500 mb-6">
        Begitu akun Anda dihapus, semua data dan riwayat pencatatan keuangan Anda akan terhapus secara permanen. Pastikan Anda telah mengunduh data yang Anda butuhkan sebelum menghapus akun.
    </p>

    <x-ui.button variant="destructive" wire:click="confirmUserDeletion">
        {{ __('Delete Account') }}
    </x-ui.button>

    <x-ui.modal wire:model="confirmingUserDeletion">
        <form wire:submit="deleteUser">
            <h2 class="text-lg font-medium text-slate-900">
                Apakah Anda yakin ingin menghapus akun?
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                Begitu akun Anda dihapus, semua data dan riwayat pencatatan keuangan Anda akan terhapus secara permanen. Masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.
            </p>

            <div class="mt-6">
                <x-ui.label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-ui.input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-ui.button variant="outline" type="button" wire:click="closeModal">
                    Batal
                </x-ui.button>

                <x-ui.button variant="destructive" type="submit">
                    Hapus Akun Permanen
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</section>
