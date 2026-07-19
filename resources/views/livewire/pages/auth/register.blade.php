<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $route = auth()->user()->role === 'admin'
            ? route('admin.dashboard', absolute: false)
            : route('user.dashboard', absolute: false);

        $this->redirect($route, navigate: true);
    }
}; ?>

<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Buat akun baru</h2>
        <p class="text-gray-500 mt-1 text-sm">Mulai kelola keuangan Anda hari ini</p>
    </div>

    {{-- Google Register Button --}}
    <a href="{{ route('google.redirect') }}"
       class="google-btn flex items-center justify-center gap-3 w-full py-3 px-4 rounded-xl text-sm font-medium text-gray-700 mb-6">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Daftar dengan Google
    </a>

    {{-- Divider --}}
    <div class="flex items-center gap-3 divider-line mb-6">
        <span class="text-xs text-gray-400 px-2 whitespace-nowrap">atau daftar dengan email</span>
    </div>

    {{-- Register Form --}}
    <form wire:submit="register" class="space-y-4">
        <div class="space-y-2">
            <x-ui.label>Nama Lengkap</x-ui.label>
            <x-ui.input
                wire:model="name"
                id="name"
                type="text"
                placeholder="Nama Anda"
                required
                autofocus
                autocomplete="name"
            />
            @error("name") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <x-ui.label>Alamat Email</x-ui.label>
            <x-ui.input
                wire:model="email"
                id="email"
                type="email"
                placeholder="nama@email.com"
                required
                autocomplete="username"
            />
            @error("email") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <x-ui.label>Password</x-ui.label>
            <x-ui.input
                wire:model="password"
                id="password"
                type="password"
                placeholder="Min. 8 karakter"
                required
                autocomplete="new-password"
            />
            @error("password") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <x-ui.label>Konfirmasi Password</x-ui.label>
            <x-ui.input
                wire:model="password_confirmation"
                id="password_confirmation"
                type="password"
                placeholder="Ulangi password"
                required
                autocomplete="new-password"
            />
            @error("password_confirmation") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
        </div>

        <x-ui.button type="submit" variant="default" class="w-full mt-2">
            Buat Akun
        </x-ui.button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Sudah punya akun?
        <a href="{{ route('login') }}" wire:navigate class="text-indigo-600 font-semibold hover:text-indigo-800 transition-colors">
            Masuk di sini
        </a>
    </p>
</div>
