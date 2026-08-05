<?php

namespace App\Livewire\Siswa;

use App\Models\Content;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ContentLibrary extends Component
{
    use WithPagination;

    #[Url]
    public string $typeFilter = '';

    #[Computed]
    public function contents()
    {
        return Content::query()
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->orderByDesc('published_at')
            ->paginate(9);
    }

    public function render()
    {
        return view('livewire.siswa.content-library');
    }
}
