<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-5">
        <x-neu-card padding="p-6 sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </x-neu-card>

        <x-neu-card padding="p-6 sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </x-neu-card>

        <x-neu-card padding="p-6 sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </x-neu-card>
    </div>
</x-app-layout>
