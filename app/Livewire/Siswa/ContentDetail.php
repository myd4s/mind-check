<?php

namespace App\Livewire\Siswa;

use App\Models\Content;
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

    public function render()
    {
        return view('livewire.siswa.content-detail');
    }
}
