<?php

use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('dashboard', 'admin.dashboard')->name('dashboard');
    // We can move profile here or keep it shared
});

Route::middleware(['auth', 'verified', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Livewire\Volt\Volt::route('pemasukan', 'income-manager')->name('income');
    Livewire\Volt\Volt::route('pengeluaran', 'expense-manager')->name('expense');
    Livewire\Volt\Volt::route('pinjaman', 'loan-manager')->name('loan');
    Livewire\Volt\Volt::route('hutang', 'debt-manager')->name('debt');
    Livewire\Volt\Volt::route('laporan', 'report-dashboard')->name('report');
    Livewire\Volt\Volt::route('kategori', 'category-manager')->name('category');
});

Route::middleware('auth')->group(function () {
    Route::view('profile', 'profile')->name('profile');
});

// Google OAuth
Route::middleware('guest')->group(function () {
    Route::get('auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

require __DIR__.'/auth.php';

