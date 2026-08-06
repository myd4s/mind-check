@php
    $isVideo = $content->type === 'video';
    $coverClass = $isVideo ? 'bg-bubblegum-100 text-bubblegum-500' : 'bg-secondary-100 text-secondary-500';
    $badgeClass = $isVideo ? 'bg-bubblegum-100 text-bubblegum-700' : 'bg-secondary-100 text-secondary-700';
@endphp

<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Detail Konten') }}
        </h2>
    </x-slot>

    <div class="space-y-5">
        <a
            href="{{ route('siswa.content-library') }}"
            wire:navigate
            class="neu-pressable inline-flex items-center gap-1.5 rounded-2xl neu-raised-sm px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary-600"
        >
            <x-icon.chevron direction="left" class="h-4 w-4" /> {{ __('Kembali ke Literasi') }}
        </a>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="min-w-0">
                <x-neu-card padding="p-0">
                    @if ($isVideo && $content->video_url)
                        <div class="aspect-video overflow-hidden rounded-t-3xl bg-slate-900">
                            <iframe src="{{ $content->embedUrl() }}" class="h-full w-full" frameborder="0" allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="flex h-48 items-center justify-center rounded-t-3xl sm:h-56 {{ $coverClass }}">
                            <x-dynamic-component :component="'icon.' . ($isVideo ? 'video-camera' : 'document-text')" class="h-16 w-16" />
                        </div>
                    @endif

                    <div class="space-y-4 p-6 sm:p-8">
                        <div>
                            <span class="inline-flex w-fit items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $badgeClass }}">
                                {{ $content->type }}
                            </span>

                            <h1 class="mt-3 font-display text-2xl font-semibold text-slate-800 sm:text-3xl">{{ $content->title }}</h1>

                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                                <span class="inline-flex items-center gap-1.5">
                                    <x-icon.user-circle class="h-4 w-4" />
                                    {{ $content->author ?: __('MindCheck') }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <x-icon.calendar class="h-4 w-4" />
                                    {{ $content->published_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>

                        <p class="max-w-3xl whitespace-pre-line text-sm leading-relaxed text-slate-600 sm:text-base">{{ $content->description }}</p>
                    </div>
                </x-neu-card>
            </div>

            <div class="space-y-5">
                <x-neu-card>
                    <h3 class="font-display text-sm font-semibold uppercase tracking-wide text-slate-500">
                        {{ __('Konten Lainnya') }}
                    </h3>

                    <div class="mt-4 space-y-3">
                        @forelse ($this->otherContents as $other)
                            @php
                                $otherIsVideo = $other->type === 'video';
                                $otherIconClass = $otherIsVideo ? 'bg-bubblegum-100 text-bubblegum-500' : 'bg-secondary-100 text-secondary-500';
                            @endphp
                            <a
                                href="{{ route('siswa.content-detail', $other) }}"
                                wire:navigate
                                wire:key="other-{{ $other->id }}"
                                class="group flex items-start gap-3 rounded-2xl p-2.5 transition-colors hover:bg-surface"
                            >
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $otherIconClass }}">
                                    <x-dynamic-component :component="'icon.' . ($otherIsVideo ? 'video-camera' : 'document-text')" class="h-5 w-5" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-slate-700 line-clamp-2 group-hover:text-primary-600">{{ $other->title }}</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">{{ $other->published_at->translatedFormat('d M Y') }}</span>
                                </span>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">{{ __('Belum ada konten lain.') }}</p>
                        @endforelse
                    </div>

                    <a
                        href="{{ route('siswa.content-library') }}"
                        wire:navigate
                        class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700"
                    >
                        {{ __('Lihat semua literasi') }}
                        <x-icon.chevron direction="right" class="h-3.5 w-3.5" />
                    </a>
                </x-neu-card>
            </div>
        </div>
    </div>
</div>
