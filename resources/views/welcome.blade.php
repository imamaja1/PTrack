<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PTrack - Sistem Keuangan Modern</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234f46e5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='22 12 18 12 15 21 9 3 6 12 2 12'></polyline></svg>">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-grid-pattern {
            background-image: linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-900 selection:bg-indigo-500 selection:text-white">

    <!-- Navigation -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md z-50 border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-slate-900">PTrack</span>
                </div>
                
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#features" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition">Fitur</a>
                    <a href="#pricing" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition">Harga</a>
                    
                    @if (Route::has('login'))
                        @auth
                            @php
                                $dashboardRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard');
                            @endphp
                            <a href="{{ $dashboardRoute }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm font-semibold bg-indigo-600 text-white px-5 py-2.5 rounded-full hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/30 transition-all duration-300">
                                    Daftar Gratis
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 bg-grid-pattern opacity-40"></div>
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-indigo-400/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-400/20 blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8">
                Kelola Arus Kas <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">Lebih Cerdas & Akurat</span>
            </h1>
            <p class="mt-4 text-xl text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                Platform pencatatan keuangan multi-user terbaik. Dapatkan laporan real-time, grafik interaktif, dan kontrol penuh atas setiap pengeluaran Anda.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-8 py-4 rounded-full font-semibold text-lg shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                    Mulai Sekarang 
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="#features" class="bg-white text-slate-700 border border-slate-200 px-8 py-4 rounded-full font-semibold text-lg hover:bg-slate-50 hover:text-indigo-600 transition-all duration-300 flex items-center justify-center">
                    Pelajari Fitur
                </a>
            </div>
            
            <!-- Dashboard Preview Mockup -->
            <div class="mt-20 relative mx-auto max-w-5xl">
                <div class="rounded-2xl border border-slate-200/60 bg-white shadow-2xl p-2 md:p-4 backdrop-blur-sm bg-white/50">
                    <div class="rounded-xl overflow-hidden border border-slate-100 relative bg-slate-50">
                        <div class="absolute top-0 w-full h-10 bg-slate-100 border-b border-slate-200 flex items-center px-4 gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="pt-10 p-6 md:p-10 grid grid-cols-3 gap-6">
                            <div class="h-24 bg-white rounded-lg shadow-sm border border-slate-100 mt-10"></div>
                            <div class="h-24 bg-white rounded-lg shadow-sm border border-slate-100 mt-10"></div>
                            <div class="h-24 bg-white rounded-lg shadow-sm border border-slate-100 mt-10"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl mb-4">Semua yang Anda butuhkan</h2>
                <p class="text-lg text-slate-600">PTrack dirancang khusus untuk memberikan pengalaman pencatatan keuangan yang cepat, aman, dan tanpa kerumitan.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300 hover:-translate-y-2 group">
                    <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Grafik Real-time</h3>
                    <p class="text-slate-600 leading-relaxed">Pantau pertumbuhan keuangan Anda dengan visualisasi chart dinamis yang merespon setiap perubahan secara instan.</p>
                </div>
                
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl hover:shadow-blue-100 transition-all duration-300 hover:-translate-y-2 group">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Privasi & Keamanan</h3>
                    <p class="text-slate-600 leading-relaxed">Arsitektur multi-user kami memastikan data Anda terisolasi sempurna. Hanya Anda yang bisa mengakses transaksi Anda.</p>
                </div>
                
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl hover:shadow-purple-100 transition-all duration-300 hover:-translate-y-2 group">
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Sangat Cepat (SPA)</h3>
                    <p class="text-slate-600 leading-relaxed">Berkat teknologi Livewire & Flux UI, interaksi aplikasi terasa instan tanpa perlu memuat ulang halaman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-slate-50 border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl mb-4">Mulai Secara Gratis</h2>
                <p class="text-lg text-slate-600">Nikmati seluruh fitur unggulan kami tanpa biaya langganan untuk penggunaan personal.</p>
            </div>
            
            <div class="max-w-lg mx-auto rounded-3xl bg-white shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
                <div class="p-8 sm:p-10">
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Personal Plan</h3>
                    <div class="flex items-baseline gap-2 mb-6">
                        <span class="text-5xl font-extrabold tracking-tight text-slate-900">Rp 0</span>
                        <span class="text-slate-500 font-medium">/ selamanya</span>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-700">Unlimited Transaksi</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-700">Dashboard Interaktif & Chart</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-700">Akses Kapan Saja</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-700">Premium Flux UI Design</span>
                        </li>
                    </ul>
                    
                    <a href="{{ route('register') }}" class="block w-full bg-slate-900 text-white text-center px-6 py-4 rounded-xl font-bold hover:bg-slate-800 transition-colors">
                        Buat Akun Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md shadow-indigo-600/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-900">PTrack</span>
            </div>
            <p class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} PTrack. All rights reserved.
            </p>
        </div>
    </footer>

</body>
</html>
