<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PTrack') }}</title>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234f46e5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='22 12 18 12 15 21 9 3 6 12 2 12'></polyline></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        <flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
            
            <flux:brand href="#" name="PTrack" class="px-2">
                <x-slot name="logo">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md shadow-indigo-600/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                </x-slot>
            </flux:brand>

            <flux:navlist variant="outline">
                @php
                    $dashboardRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard');
                    $isDashboard = request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard');
                @endphp
                <flux:navlist.item icon="home" :href="$dashboardRoute" :current="$isDashboard" wire:navigate>Dashboard</flux:navlist.item>
                
                @if(auth()->user()->role === 'admin')
                    <!-- Admin specific links can go here -->
                @else
                    <flux:navlist.group heading="Transaksi" class="mt-4">
                        <flux:navlist.item icon="arrow-down-circle" :href="route('user.income')" :current="request()->routeIs('user.income')" wire:navigate>Pemasukan</flux:navlist.item>
                        <flux:navlist.item icon="arrow-up-circle" :href="route('user.expense')" :current="request()->routeIs('user.expense')" wire:navigate>Pengeluaran</flux:navlist.item>
                    </flux:navlist.group>
                    
                    <flux:navlist.group heading="Hutang & Piutang" class="mt-4">
                        <flux:navlist.item icon="banknotes" :href="route('user.debt')" :current="request()->routeIs('user.debt')" wire:navigate>Hutang</flux:navlist.item>
                        <flux:navlist.item icon="users" :href="route('user.loan')" :current="request()->routeIs('user.loan')" wire:navigate>Pinjaman</flux:navlist.item>
                    </flux:navlist.group>

                    <flux:navlist.group heading="Laporan" class="mt-4">
                        <flux:navlist.item icon="chart-pie" :href="route('user.report')" :current="request()->routeIs('user.report')" wire:navigate>Laporan Keuangan</flux:navlist.item>
                    </flux:navlist.group>

                    <flux:navlist.group heading="Pengaturan" class="mt-4">
                        <flux:navlist.item icon="squares-2x2" :href="route('user.category')" :current="request()->routeIs('user.category')" wire:navigate>Kategori</flux:navlist.item>
                    </flux:navlist.group>
                @endif
            </flux:navlist>

            <flux:spacer />

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:profile name="{{ auth()->user()->name }}" icon-trailing="chevron-up" />

                <flux:menu>
                    <flux:menu.item icon="user" :href="route('profile')" wire:navigate>Profil</flux:menu.item>
                    <flux:menu.separator />
                    <livewire:logout-button />
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />
            
            <flux:spacer />
            
            <flux:dropdown position="top" align="end">
                <flux:profile name="{{ auth()->user()->name }}" icon-trailing="chevron-down" />

                <flux:menu>
                    <flux:menu.item icon="user" :href="route('profile')" wire:navigate>Profil</flux:menu.item>
                    <flux:menu.separator />
                    <livewire:logout-button />
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:main>
            @if (isset($header))
                <div class="mb-6">
                    {{ $header }}
                    <flux:separator variant="subtle" class="mt-4" />
                </div>
            @endif

            {{ $slot }}
        </flux:main>

        <flux:toast position="top right" />

        @fluxScripts
        
        <!-- Global Loading Screen for Navigation Transitions -->
        <div id="global-loader" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-white/70 dark:bg-zinc-900/70 backdrop-blur-sm transition-opacity duration-300 opacity-0">
            <div class="flex flex-col items-center gap-3">
                <!-- Spinner icon -->
                <svg class="animate-spin h-10 w-10 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Memuat data...</span>
            </div>
        </div>

        <script>
            document.addEventListener('livewire:navigate', () => {
                const loader = document.getElementById('global-loader');
                if (loader) {
                    loader.style.display = 'flex';
                    setTimeout(() => loader.classList.remove('opacity-0'), 10);
                }
            });
            document.addEventListener('livewire:navigated', () => {
                const loader = document.getElementById('global-loader');
                if (loader) {
                    loader.classList.add('opacity-0');
                    setTimeout(() => loader.style.display = 'none', 300);
                }
            });
        </script>
    </body>
</html>
