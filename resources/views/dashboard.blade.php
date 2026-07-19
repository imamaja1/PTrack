<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-bold tracking-tight">{{ __('Dashboard') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <livewire:dashboard-summary />
        <livewire:financial-chart />
    </div>
</x-app-layout>
