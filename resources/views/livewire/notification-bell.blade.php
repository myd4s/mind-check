<div class="relative" x-data="{ open: false }" wire:poll.30s="$refresh">
    <button @click="open = ! open" type="button" class="relative inline-flex items-center justify-center w-9 h-9 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9" />
        </svg>

        @if ($this->unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-rose-600 rounded-full">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-on:click.outside="open = false"
        x-transition
        class="absolute right-0 z-50 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden"
        style="display: none;"
    >
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-semibold text-gray-900">Notifikasi</span>
            @if ($this->unreadCount > 0)
                <button wire:click="markAllAsRead" type="button" class="text-xs text-indigo-600 hover:text-indigo-700">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse ($this->notifications as $notification)
                <button
                    type="button"
                    wire:click="markAsRead('{{ $notification->id }}')"
                    class="w-full text-left px-4 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-indigo-50/40' }}"
                >
                    <p class="text-sm text-gray-700">{{ $notification->data['message'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </button>
            @empty
                <p class="px-4 py-6 text-sm text-gray-500 text-center">Belum ada notifikasi.</p>
            @endforelse
        </div>
    </div>
</div>
