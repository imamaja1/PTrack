<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-background antialiased">
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
</head>
<body class="h-full flex bg-background text-foreground" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-background/80 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col border-r bg-muted/20 transition-transform lg:static lg:translate-x-0">
        <div class="flex h-14 items-center px-4 border-b">
            <div class="flex items-center gap-2 font-semibold">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                </div>
                PTrack
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
            @php
                $dashboardRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard');
                $isDashboard = request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard');
            @endphp
            
            <ul class="space-y-1">
                <li>
                    <a href="{{ $dashboardRoute }}" wire:navigate class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ $isDashboard ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                </li>
            </ul>

            @if(auth()->user()->role !== 'admin')
                <div>
                    <h4 class="mb-2 px-3 text-xs font-semibold tracking-tight text-muted-foreground uppercase">Transaksi</h4>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('user.income') }}" wire:navigate class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('user.income') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                                Pemasukan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.expense') }}" wire:navigate class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('user.expense') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                                Pengeluaran
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="mb-2 px-3 text-xs font-semibold tracking-tight text-muted-foreground uppercase">Hutang & Piutang</h4>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('user.debt') }}" wire:navigate class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('user.debt') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Hutang
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.loan') }}" wire:navigate class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('user.loan') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Pinjaman
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="mb-2 px-3 text-xs font-semibold tracking-tight text-muted-foreground uppercase">Laporan</h4>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('user.report') }}" wire:navigate class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('user.report') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                                Laporan Keuangan
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="mb-2 px-3 text-xs font-semibold tracking-tight text-muted-foreground uppercase">Pengaturan</h4>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('user.category', ['type' => 'income']) }}" wire:navigate class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('user.category') && request()->type == 'income' ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                Kategori Pemasukan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.category', ['type' => 'expense']) }}" wire:navigate class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('user.category') && request()->type == 'expense' ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                Kategori Pengeluaran
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </nav>

        <div class="border-t p-4">
            <x-ui.dropdown align="top" width="56">
                <x-slot name="trigger">
                    <button class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm font-medium hover:bg-muted transition-colors">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="flex-1 text-left truncate">{{ auth()->user()->name }}</span>
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-ui.dropdown-item href="{{ route('profile') }}">Profil</x-ui.dropdown-item>
                    <div class="h-px bg-muted my-1"></div>
                    <livewire:logout-button />
                </x-slot>
            </x-ui.dropdown>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Mobile header -->
        <header class="flex h-14 items-center border-b px-4 lg:hidden">
            <button @click="sidebarOpen = true" class="mr-4 p-2 -ml-2 text-muted-foreground">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="flex-1 font-semibold text-lg">PTrack</div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            @if (isset($header))
                <div class="mb-6 pb-4 border-b">
                    {{ $header }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- Global Loading Screen for Navigation Transitions -->
    <div id="global-loader" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-background/70 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="flex flex-col items-center gap-3">
            <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium text-muted-foreground">Memuat data...</span>
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
