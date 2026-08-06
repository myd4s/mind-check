<?php

namespace App\Livewire\Siswa;

use App\Models\Content;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ContentDetail extends Component
{
    public Content $content;

    public function mount(Content $content): void
    {
        $this->content = $content;
    }

    #[Computed]
    public function otherContents()
    {
        return Content::where('id', '!=', $this->content->id)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.siswa.content-detail');
    }
}
