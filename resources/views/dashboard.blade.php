<x-app-layout>
    <x-slot name="header">
        <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
    </x-slot>

    <div class="space-y-6">
        <livewire:dashboard-summary />
        <livewire:financial-chart />
    </div>
</x-app-layout>
