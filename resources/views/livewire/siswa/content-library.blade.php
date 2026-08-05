<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Literasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap gap-2">
                <button wire:click="$set('typeFilter', '')" class="px-3 py-1.5 rounded-md text-sm font-medium {{ $typeFilter === '' ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    {{ __('Semua') }}
                </button>
                <button wire:click="$set('typeFilter', 'artikel')" class="px-3 py-1.5 rounded-md text-sm font-medium {{ $typeFilter === 'artikel' ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    {{ __('Artikel') }}
                </button>
                <button wire:click="$set('typeFilter', 'video')" class="px-3 py-1.5 rounded-md text-sm font-medium {{ $typeFilter === 'video' ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100' }}">
                    {{ __('Video') }}
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($this->contents as $content)
                    <a href="{{ route('siswa.content-detail', $content) }}" wire:navigate wire:key="content-{{ $content->id }}" class="block bg-white shadow-sm sm:rounded-lg p-5 hover:shadow-md transition">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-secondary-100 text-secondary-700 capitalize">
                            {{ $content->type }}
                        </span>
                        <h3 class="mt-2 text-base font-semibold text-gray-900 line-clamp-2">{{ $content->title }}</h3>
                        <p class="mt-1 text-sm text-gray-500 line-clamp-3">{{ $content->description }}</p>
                        <p class="mt-3 text-xs text-gray-500">
                            {{ $content->author ?: __('MindCheck') }} &middot; {{ $content->published_at->translatedFormat('d M Y') }}
                        </p>
                    </a>
                @empty
                    <div class="col-span-full bg-white shadow-sm sm:rounded-lg p-8 text-center text-sm text-gray-500">
                        {{ __('Belum ada konten literasi.') }}
                    </div>
                @endforelse
            </div>

            <div>
                {{ $this->contents->links() }}
            </div>
        </div>
    </div>
</div>
