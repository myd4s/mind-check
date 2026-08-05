<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="mx-auto space-y-5 px-4 sm:px-6 lg:px-8">
        <x-neu-card padding="p-6 sm:p-8">
            <livewire:profile.update-profile-information-form />
        </x-neu-card>

        <x-neu-card padding="p-6 sm:p-8">
            <livewire:profile.update-password-form />
        </x-neu-card>

        <x-neu-card padding="p-6 sm:p-8">
            <livewire:profile.delete-user-form />
        </x-neu-card>
    </div>
</x-app-layout>
