<?php

namespace App\Livewire\GuruBk;

use App\Livewire\Concerns\WithTableControls;
use App\Models\Content;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ContentManagement extends Component
{
    use WithPagination, WithTableControls;

    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    public string $type = 'artikel';

    public string $video_url = '';

    public string $author = '';

    public string $published_at = '';

    public bool $showModal = false;

    public ?int $deletingId = null;

    #[Computed]
    public function contents()
    {
        return Content::query()
            ->when($this->search, fn ($query) => $query->where('title', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField ?: 'published_at', $this->sortField ? $this->sortDirection : 'desc')
            ->paginate($this->perPage);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->published_at = now()->toDateString();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $content = Content::findOrFail($id);

        $this->editingId = $content->id;
        $this->title = $content->title;
        $this->description = $content->description;
        $this->type = $content->type;
        $this->video_url = $content->video_url ?? '';
        $this->author = $content->author ?? '';
        $this->published_at = $content->published_at->toDateString();
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => ['required', Rule::in(['artikel', 'video'])],
            'video_url' => 'required_if:type,video|nullable|url|max:255',
            'author' => 'nullable|string|max:255',
            'published_at' => 'required|date',
        ]);

        $validated['video_url'] = $this->type === 'video' ? $validated['video_url'] : null;

        Content::updateOrCreate(['id' => $this->editingId], $validated);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        Content::find($this->deletingId)?->delete();
        $this->deletingId = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'description', 'type', 'video_url', 'author', 'published_at']);
        $this->type = 'artikel';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.guru-bk.content-management');
    }
}
