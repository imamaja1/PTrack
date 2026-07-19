<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PTrack — Kelola Keuangan dengan Cerdas</title>
    <meta name="description" content="Platform pencatatan keuangan pribadi terbaik. Catat pemasukan, pengeluaran, hutang, dan pinjaman dengan laporan interaktif yang mudah dipahami.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234f46e5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='22 12 18 12 15 21 9 3 6 12 2 12'></polyline></svg>">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Hero gradient mesh */
        .hero-bg {
            background: linear-gradient(135deg, #f8faff 0%, #eff3ff 40%, #f0fdf4 100%);
        }

        /* Animated gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 40%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            pointer-events: none;
        }

        /* Stat badge */
        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }

        /* Dashboard mockup */
        .mockup-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        /* Feature card hover */
        .feature-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(79, 70, 229, 0.12);
        }

        /* CTA section gradient */
        .cta-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 60%, #0ea5e9 100%);
        }

        /* Smooth scroll offset */
        section[id] { scroll-margin-top: 80px; }
    </style>
</head>
<body class="antialiased bg-white text-slate-900 selection:bg-indigo-100 selection:text-indigo-700">

    <!-- ==================== NAVIGATION ==================== -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-xl z-50 border-b border-slate-100/80" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-6xl mx-auto px-5 sm:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- Logo -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-lg flex items-center justify-center text-white shadow shadow-indigo-600/30 group-hover:shadow-indigo-600/50 transition-shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <span class="font-bold text-lg tracking-tight text-slate-900">PTrack</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            @php $dashboardRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard'); @endphp
                            <a href="{{ $dashboardRoute }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white text-sm font-semibold px-5 py-2.5 rounded-full hover:bg-indigo-700 shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/40 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors px-4 py-2 rounded-full hover:bg-slate-100">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center text-sm font-semibold bg-indigo-600 text-white px-5 py-2.5 rounded-full hover:bg-indigo-700 shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/40 transition-all duration-200">
                                    Daftar Gratis
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Mobile Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg x-show="!mobileMenuOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-cloak x-show="mobileMenuOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-cloak x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="md:hidden absolute inset-x-0 top-16 bg-white/95 backdrop-blur-xl border-b border-slate-200 shadow-xl">
            <div class="max-w-6xl mx-auto px-5 py-5 space-y-3">
                @if (Route::has('login'))
                    @auth
                        @php $dashboardRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard'); @endphp
                        <a href="{{ $dashboardRoute }}" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-indigo-600 text-white font-semibold rounded-xl shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center justify-center w-full px-4 py-3 border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition-colors text-sm">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="flex items-center justify-center w-full px-4 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition-colors text-sm">
                                Daftar Gratis — Mulai Sekarang
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- ==================== HERO ==================== -->
    <section class="hero-bg relative pt-28 pb-16 md:pt-36 md:pb-24 overflow-hidden">
        <!-- Decorative orbs -->
        <div class="orb w-96 h-96 bg-indigo-400 top-0 -right-24 -top-24"></div>
        <div class="orb w-72 h-72 bg-blue-300 bottom-0 -left-20 -bottom-20"></div>
        <div class="orb w-48 h-48 bg-emerald-300 top-1/2 right-1/4"></div>

        <div class="max-w-6xl mx-auto px-5 sm:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">

                <!-- Badge -->
                <div class="inline-flex items-center gap-2 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold px-4 py-1.5 rounded-full mb-6">
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
                    Pencatatan Keuangan Personal
                </div>

                <!-- Headline -->
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 leading-[1.08] mb-6">
                    Kendalikan Keuangan Anda
                    <span class="gradient-text block mt-1">Lebih Cerdas & Akurat</span>
                </h1>

                <!-- Subtext -->
                <p class="text-base sm:text-lg md:text-xl text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed font-normal">
                    Catat setiap pemasukan, pengeluaran, hutang, dan pinjaman dalam satu platform yang elegan — dilengkapi laporan visual real-time.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-3 mb-14">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-indigo-600 text-white px-7 py-3.5 rounded-full font-semibold text-sm shadow-xl shadow-indigo-600/30 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all duration-200">
                        Mulai Gratis Sekarang
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 bg-white text-slate-700 px-7 py-3.5 rounded-full font-semibold text-sm border border-slate-200 hover:border-indigo-300 hover:text-indigo-700 hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
                        Sudah punya akun? Masuk
                    </a>
                </div>

                <!-- Stats row -->
                <div class="flex flex-wrap justify-center gap-4 mb-16">
                    <div class="stat-pill bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Gratis Selamanya
                    </div>
                    <div class="stat-pill bg-blue-50 text-blue-700 border border-blue-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Data Aman & Privat
                    </div>
                    <div class="stat-pill bg-purple-50 text-purple-700 border border-purple-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Tampilan Real-time
                    </div>
                </div>

                <!-- App Mockup -->
                <div class="relative mx-auto max-w-4xl">
                    <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/10 to-transparent rounded-3xl blur-2xl scale-105"></div>
                    <div class="relative bg-white rounded-2xl border border-slate-200 shadow-2xl shadow-slate-900/10 overflow-hidden">
                        <!-- Browser bar -->
                        <div class="flex items-center gap-2 bg-slate-50 border-b border-slate-200 px-4 py-3">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                            </div>
                            <div class="flex-1 mx-4 bg-white rounded-md px-3 py-1 border border-slate-200 text-xs text-slate-400 text-left">
                                ptrack.app/dashboard
                            </div>
                        </div>
                        <!-- Mockup Content -->
                        <div class="p-5 bg-slate-50/50">
                            <!-- Top cards -->
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="mockup-card p-4">
                                    <div class="text-xs text-slate-400 mb-1">Saldo</div>
                                    <div class="text-sm font-bold text-slate-800">Rp 4.200.000</div>
                                    <div class="text-xs text-indigo-600 mt-1">↑ Sehat</div>
                                </div>
                                <div class="mockup-card p-4">
                                    <div class="text-xs text-slate-400 mb-1">Pemasukan</div>
                                    <div class="text-sm font-bold text-emerald-700">Rp 6.500.000</div>
                                    <div class="text-xs text-emerald-600 mt-1">↑ +12%</div>
                                </div>
                                <div class="mockup-card p-4">
                                    <div class="text-xs text-slate-400 mb-1">Pengeluaran</div>
                                    <div class="text-sm font-bold text-red-600">Rp 2.300.000</div>
                                    <div class="text-xs text-red-500 mt-1">↓ -5%</div>
                                </div>
                            </div>
                            <!-- Chart skeleton -->
                            <div class="mockup-card p-4">
                                <div class="flex items-end gap-1 h-16">
                                    <div class="flex-1 bg-indigo-200 rounded-sm" style="height:40%"></div>
                                    <div class="flex-1 bg-indigo-300 rounded-sm" style="height:65%"></div>
                                    <div class="flex-1 bg-indigo-200 rounded-sm" style="height:50%"></div>
                                    <div class="flex-1 bg-indigo-500 rounded-sm" style="height:90%"></div>
                                    <div class="flex-1 bg-indigo-200 rounded-sm" style="height:35%"></div>
                                    <div class="flex-1 bg-indigo-300 rounded-sm" style="height:70%"></div>
                                    <div class="flex-1 bg-indigo-400 rounded-sm" style="height:80%"></div>
                                </div>
                                <div class="text-xs text-slate-400 mt-2 text-center">Grafik Keuangan Bulanan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== FEATURES ==================== -->
    <section id="features" class="py-20 md:py-28 bg-white">
        <div class="max-w-6xl mx-auto px-5 sm:px-8">

            <div class="text-center max-w-2xl mx-auto mb-16">
                <p class="text-indigo-600 font-semibold text-sm mb-3 tracking-wide uppercase">Fitur Unggulan</p>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 mb-4">
                    Semua yang Anda butuhkan, <span class="gradient-text">dalam satu tempat</span>
                </h2>
                <p class="text-slate-500 leading-relaxed">Dirancang agar pencatatan keuangan terasa sederhana, namun informatif dan akurat.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                <!-- Feature 1 -->
                <div class="feature-card group p-6 rounded-2xl border border-slate-100 bg-white hover:border-indigo-100">
                    <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Catat Pemasukan</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Catat semua sumber penghasilan berdasarkan kategori yang bisa Anda buat sendiri.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card group p-6 rounded-2xl border border-slate-100 bg-white hover:border-red-100">
                    <div class="w-11 h-11 bg-red-50 text-red-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-red-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Pantau Pengeluaran</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Jangan biarkan uang habis tanpa jejak. Lacak setiap pengeluaran sekecil apapun.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card group p-6 rounded-2xl border border-slate-100 bg-white hover:border-orange-100">
                    <div class="w-11 h-11 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-orange-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Manajemen Hutang</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Catat hutang yang harus dibayar agar tidak lupa. Status pembayaran dapat diperbarui kapan saja.</p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card group p-6 rounded-2xl border border-slate-100 bg-white hover:border-blue-100">
                    <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Catat Pinjaman</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Catat uang Anda yang dipinjam orang lain dan kelola pelunasannya secara bertahap.</p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card group p-6 rounded-2xl border border-slate-100 bg-white hover:border-purple-100">
                    <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Laporan Visual</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Grafik interaktif tahunan dan bulanan yang informatif untuk memahami tren keuangan Anda.</p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card group p-6 rounded-2xl border border-slate-100 bg-white hover:border-indigo-100">
                    <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Kategori Kustom</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Buat kategori Anda sendiri lengkap dengan ikon dan warna untuk mengelompokkan transaksi.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ==================== CTA ==================== -->
    <section class="py-16 md:py-20 bg-slate-50">
        <div class="max-w-6xl mx-auto px-5 sm:px-8">
            <div class="cta-bg rounded-3xl p-8 md:p-14 text-center overflow-hidden relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
                <div class="relative">
                    <h2 class="text-2xl md:text-4xl font-bold text-white mb-4 tracking-tight">
                        Siap mengelola keuangan lebih baik?
                    </h2>
                    <p class="text-indigo-200 mb-8 text-base md:text-lg max-w-xl mx-auto">
                        Daftar sekarang, gratis selamanya. Mulai catat keuangan Anda dalam hitungan menit.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-white text-indigo-700 px-7 py-3.5 rounded-full font-bold text-sm hover:bg-indigo-50 transition-colors shadow-lg">
                            Buat Akun Sekarang — Gratis
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 bg-transparent text-white border border-white/30 px-7 py-3.5 rounded-full font-semibold text-sm hover:bg-white/10 transition-colors">
                            Sudah daftar? Masuk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== FOOTER ==================== -->
    <footer class="bg-white border-t border-slate-100 py-8">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-lg flex items-center justify-center text-white">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <span class="font-bold text-base text-slate-900">PTrack</span>
            </div>
            <p class="text-sm text-slate-400">
                &copy; {{ date('Y') }} PTrack. Dibuat dengan ❤️ untuk keuangan Anda.
            </p>
        </div>
    </footer>

</body>
</html>
