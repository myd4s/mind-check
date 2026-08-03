<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <p class="text-gray-600">{{ __("You're logged in!") }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
