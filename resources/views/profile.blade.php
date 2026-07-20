<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                    Pengaturan Profil
                </h2>
                <p class="text-sm text-slate-500 mt-1">Kelola informasi pribadi dan keamanan akun Anda.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Profile Information Card -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-0.5">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="font-semibold text-slate-800">Informasi Profil</h3>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>
        </div>

        <!-- Update Password Card -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-0.5">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="font-semibold text-slate-800">Keamanan & Password</h3>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>
        </div>

        <!-- Delete Account Card -->
        <div class="bg-white rounded-2xl border border-red-100 shadow-xl shadow-red-100/40 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-red-100/50 hover:-translate-y-0.5">
            <div class="border-b border-red-50 bg-red-50/30 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="bg-red-100 text-red-600 p-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </div>
                    <h3 class="font-semibold text-red-600">Hapus Akun</h3>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
