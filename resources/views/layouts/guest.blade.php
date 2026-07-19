<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PTrack') }}</title>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234f46e5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='22 12 18 12 15 21 9 3 6 12 2 12'></polyline></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * { font-family: 'Inter', sans-serif; }

            .auth-gradient {
                background: linear-gradient(135deg, #1e1b4b 0%, #312e81 30%, #4338ca 60%, #6366f1 100%);
            }

            .glass-card {
                background: rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.15);
            }

            .feature-item {
                animation: slideInLeft 0.6s ease forwards;
                opacity: 0;
            }
            .feature-item:nth-child(1) { animation-delay: 0.1s; }
            .feature-item:nth-child(2) { animation-delay: 0.25s; }
            .feature-item:nth-child(3) { animation-delay: 0.4s; }

            @keyframes slideInLeft {
                from { opacity: 0; transform: translateX(-20px); }
                to   { opacity: 1; transform: translateX(0); }
            }

            .form-panel {
                animation: fadeInRight 0.5s ease forwards;
            }
            @keyframes fadeInRight {
                from { opacity: 0; transform: translateX(20px); }
                to   { opacity: 1; transform: translateX(0); }
            }

            .orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(60px);
                opacity: 0.3;
                pointer-events: none;
            }

            .google-btn {
                transition: all 0.2s ease;
                background: white;
                border: 1.5px solid #e5e7eb;
            }
            .google-btn:hover {
                background: #f9fafb;
                border-color: #d1d5db;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                transform: translateY(-1px);
            }

            .divider-line::before, .divider-line::after {
                content: '';
                flex: 1;
                height: 1px;
                background: #e5e7eb;
            }
        </style>
    </head>

    <body class="antialiased" style="background: #f8fafc; min-height: 100vh;">
        <div class="min-h-screen flex">

            {{-- LEFT PANEL: Branding (hidden on mobile) --}}
            <div class="hidden lg:flex lg:w-[45%] xl:w-1/2 auth-gradient relative overflow-hidden flex-col justify-between p-12">

                {{-- Decorative orbs --}}
                <div class="orb w-80 h-80 bg-purple-400 -top-20 -left-20"></div>
                <div class="orb w-64 h-64 bg-blue-400 bottom-10 -right-10"></div>
                <div class="orb w-48 h-48 bg-indigo-300 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>

                {{-- Logo & Brand --}}
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                        </div>
                        <span class="text-white font-bold text-xl tracking-tight">PTrack</span>
                    </div>
                </div>

                {{-- Hero Content --}}
                <div class="relative z-10 flex-1 flex flex-col justify-center">
                    <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight mb-4">
                        Kelola Keuangan<br>
                        <span class="text-indigo-200">Lebih Cerdas</span>
                    </h1>
                    <p class="text-indigo-200 text-lg mb-10 leading-relaxed">
                        Pantau pemasukan, pengeluaran, dan tren keuangan Anda dalam satu dasbor yang elegan.
                    </p>

                    {{-- Feature List --}}
                    <div class="space-y-4">
                        <div class="feature-item flex items-center gap-4">
                            <div class="glass-card w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-medium text-sm">Grafik Real-Time</p>
                                <p class="text-indigo-300 text-xs">Visualisasi data harian, mingguan & bulanan</p>
                            </div>
                        </div>
                        <div class="feature-item flex items-center gap-4">
                            <div class="glass-card w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-medium text-sm">Pencatatan Mudah</p>
                                <p class="text-indigo-300 text-xs">Tambah transaksi dalam hitungan detik</p>
                            </div>
                        </div>
                        <div class="feature-item flex items-center gap-4">
                            <div class="glass-card w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-medium text-sm">Aman & Terenkripsi</p>
                                <p class="text-indigo-300 text-xs">Data Anda terlindungi sepenuhnya</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom stats bar --}}
                <div class="relative z-10 glass-card rounded-2xl p-4 flex justify-around">
                    <div class="text-center">
                        <p class="text-white font-bold text-lg">100%</p>
                        <p class="text-indigo-300 text-xs">Privasi Data</p>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-white font-bold text-lg">Gratis</p>
                        <p class="text-indigo-300 text-xs">Selamanya</p>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-white font-bold text-lg">24/7</p>
                        <p class="text-indigo-300 text-xs">Tersedia</p>
                    </div>
                </div>
            </div>

            {{-- RIGHT PANEL: Auth Form --}}
            <div class="w-full lg:w-[55%] xl:w-1/2 flex items-center justify-center p-6 sm:p-10">
                <div class="w-full max-w-md form-panel">

                    {{-- Mobile Logo --}}
                    <div class="flex justify-center mb-8 lg:hidden">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </div>
                            <span class="text-gray-800 font-bold text-xl">PTrack</span>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
