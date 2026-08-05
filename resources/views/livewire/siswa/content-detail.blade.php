<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Konten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <a href="{{ route('siswa.content-library') }}" wire:navigate class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                &larr; {{ __('Kembali ke Literasi') }}
            </a>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-secondary-100 text-secondary-700 capitalize">
                        {{ $content->type }}
                    </span>
                    <h1 class="mt-2 text-xl font-semibold text-gray-900">{{ $content->title }}</h1>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $content->author ?: __('MindCheck') }} &middot; {{ $content->published_at->translatedFormat('d M Y') }}
                    </p>
                </div>

                @if ($content->type === 'video' && $content->video_url)
                    <div class="aspect-video rounded-md overflow-hidden bg-gray-100">
                        <iframe src="{{ $content->embedUrl() }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                    </div>
                @endif

                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $content->description }}</p>
            </div>
        </div>
    </div>
</div>
