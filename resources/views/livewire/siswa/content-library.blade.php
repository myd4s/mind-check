<div>
    <x-slot name="header">
        <h2 class="font-display text-2xl font-semibold text-slate-800">
            {{ __('Literasi') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ __('Baca artikel dan tonton video seputar kesehatan mental & pengelolaan stress.') }}
        </p>
    </x-slot>

    <div class="space-y-5">
        <div class="flex flex-wrap gap-2">
            <button
                type="button"
                wire:click="$set('typeFilter', '')"
                class="neu-pressable inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-semibold {{ $typeFilter === '' ? 'bg-primary-600 text-white shadow-neu-sm' : 'neu-raised-sm text-slate-600 hover:text-primary-600' }}"
            >
                <x-icon.sparkles class="h-4 w-4" /> {{ __('Semua') }}
            </button>
            <button
                type="button"
                wire:click="$set('typeFilter', 'artikel')"
                class="neu-pressable inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-semibold {{ $typeFilter === 'artikel' ? 'bg-secondary-600 text-white shadow-neu-sm' : 'neu-raised-sm text-slate-600 hover:text-secondary-600' }}"
            >
                <x-icon.document-text class="h-4 w-4" /> {{ __('Artikel') }}
            </button>
            <button
                type="button"
                wire:click="$set('typeFilter', 'video')"
                class="neu-pressable inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-semibold {{ $typeFilter === 'video' ? 'bg-bubblegum-600 text-white shadow-neu-sm' : 'neu-raised-sm text-slate-600 hover:text-bubblegum-600' }}"
            >
                <x-icon.video-camera class="h-4 w-4" /> {{ __('Video') }}
            </button>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
            @forelse ($this->contents as $content)
                @php
                    $isVideo = $content->type === 'video';
                    $coverClass = $isVideo ? 'bg-bubblegum-100 text-bubblegum-500' : 'bg-secondary-100 text-secondary-500';
                    $badgeClass = $isVideo ? 'bg-bubblegum-100 text-bubblegum-700' : 'bg-secondary-100 text-secondary-700';
                @endphp
                <a
                    href="{{ route('siswa.content-detail', $content) }}"
                    wire:navigate
                    wire:key="content-{{ $content->id }}"
                    class="group neu-card flex flex-col overflow-hidden transition-transform duration-200 hover:-translate-y-1"
                >
                    <div class="relative flex h-32 items-center justify-center {{ $coverClass }}">
                        <x-dynamic-component :component="'icon.' . ($isVideo ? 'video-camera' : 'document-text')" class="h-12 w-12" />
                        @if ($isVideo)
                            <span class="absolute inset-0 flex items-center justify-center bg-slate-900/0 transition-colors group-hover:bg-slate-900/10">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/90 text-bubblegum-600 opacity-0 shadow-neu-sm transition-opacity group-hover:opacity-100">
                                    <x-icon.video-camera class="h-5 w-5" />
                                </span>
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <span class="inline-flex w-fit items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $badgeClass }}">
                            {{ $content->type }}
                        </span>

                        <h3 class="mt-2.5 font-display text-base font-semibold text-slate-800 line-clamp-2">{{ $content->title }}</h3>
                        <p class="mt-1.5 flex-1 text-sm text-slate-500 line-clamp-3">{{ $content->description }}</p>

                        <div class="mt-4 flex items-center justify-between gap-2 border-t border-surface-inset pt-3.5">
                            <span class="truncate text-xs text-slate-500">
                                {{ $content->author ?: __('MindCare') }} &middot; {{ $content->published_at->translatedFormat('d M Y') }}
                            </span>
                            <span class="inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-primary-600">
                                {{ $isVideo ? __('Tonton') : __('Baca') }}
                                <x-icon.chevron direction="right" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full">
                    <x-neu-card>
                        <x-empty-state
                            icon="book-open"
                            :title="__('Belum ada konten literasi')"
                            :description="__('Artikel dan video seputar kesehatan mental akan muncul di sini.')"
                        />
                    </x-neu-card>
                </div>
            @endforelse
        </div>

        @if ($this->contents->hasPages())
            <div class="flex justify-center">
                {{ $this->contents->links() }}
            </div>
        @endif
    </div>
</div>
