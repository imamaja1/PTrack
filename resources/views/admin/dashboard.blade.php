<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold tracking-tight">{{ __('Admin Dashboard') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <x-ui.card><x-ui.card-content class="pt-6">
            <h1 class="text-xl font-bold tracking-tight">Selamat Datang, Admin!</h1>
            <p class="text-sm text-muted-foreground">Anda dapat memantau dan mengelola seluruh pengguna yang terdaftar di Sistem Keuangan ini.</p>
        </x-ui.card-content></x-ui.card>

        <livewire:admin-user-list />
    </div>
</x-app-layout>
