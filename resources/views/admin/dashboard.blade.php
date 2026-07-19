<x-app-layout>
    <x-slot name="header">
        <flux:heading size="xl" level="1">{{ __('Admin Dashboard') }}</flux:heading>
    </x-slot>

    <div class="space-y-6">
        <flux:card>
            <flux:heading size="lg" class="mb-2">Selamat Datang, Admin!</flux:heading>
            <flux:subheading>Anda dapat memantau dan mengelola seluruh pengguna yang terdaftar di Sistem Keuangan ini.</flux:subheading>
        </flux:card>

        <livewire:admin-user-list />
    </div>
</x-app-layout>
