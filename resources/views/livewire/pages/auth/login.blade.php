<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        $route = auth()->user()->role === 'admin'
            ? route('admin.dashboard', absolute: false)
            : route('user.dashboard', absolute: false);

        $this->redirectIntended(default: $route, navigate: true);
    }
}; ?>

<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Selamat datang kembali</h2>
        <p class="text-gray-500 mt-1 text-sm">Masukkan akun Anda untuk melanjutkan</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('status') }}
        </div>
    @endif
    
    @error('google')
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            {{ $message }}
        </div>
    @enderror

    {{-- Google Login Button --}}
    <a href="{{ route('google.redirect') }}"
       class="google-btn flex items-center justify-center gap-3 w-full py-3 px-4 rounded-xl text-sm font-medium text-gray-700 mb-6">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Masuk dengan Google
    </a>

    {{-- Divider --}}
    <div class="flex items-center gap-3 divider-line mb-6">
        <span class="text-xs text-gray-400 px-2 whitespace-nowrap">atau masuk dengan email</span>
    </div>

    {{-- Login Form --}}
    <form wire:submit="login" class="space-y-4">
        <div class="space-y-2">
            <x-ui.label>Alamat Email</x-ui.label>
            <x-ui.input
                wire:model="form.email"
                id="email"
                type="email"
                placeholder="nama@email.com"
                required
                autofocus
                autocomplete="username"
            />
            @error("form.email") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <x-ui.label>Password</x-ui.label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                        Lupa password?
                    </a>
                @endif
            </div>
            <x-ui.input
                wire:model="form.password"
                id="password"
                type="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            />
            @error("form.password") <span class="text-sm font-medium text-destructive">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-2 mt-1">
            <flux:checkbox wire:model="form.remember" id="remember" />
            <label for="remember" class="text-sm text-gray-600 cursor-pointer">Ingat saya</label>
        </div>

        <x-ui.button type="submit" variant="default" class="w-full mt-2">
            Masuk
        </x-ui.button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Belum punya akun?
        <a href="{{ route('register') }}" wire:navigate class="text-indigo-600 font-semibold hover:text-indigo-800 transition-colors">
            Daftar sekarang
        </a>
    </p>
</div>
