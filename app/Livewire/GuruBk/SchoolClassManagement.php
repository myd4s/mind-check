<?php

namespace App\Livewire\GuruBk;

use App\Livewire\Concerns\WithTableControls;
use App\Models\SchoolClass;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SchoolClassManagement extends Component
{
    use WithPagination, WithTableControls;

    public ?int $editingId = null;

    public string $name = '';

    public string $grade_level = '';

    public bool $showModal = false;

    public ?int $deletingId = null;

    public ?string $deleteError = null;

    #[Computed]
    public function schoolClasses()
    {
        return SchoolClass::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField ?: 'grade_level', $this->sortField ? $this->sortDirection : 'asc')
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $class = SchoolClass::findOrFail($id);

        $this->editingId = $class->id;
        $this->name = $class->name;
        $this->grade_level = $class->grade_level;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
        ]);

        SchoolClass::updateOrCreate(['id' => $this->editingId], $validated);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->deleteError = null;
    }

    public function delete(): void
    {
        $class = SchoolClass::find($this->deletingId);

        if ($class && $class->classHistories()->where('status', 'aktif')->exists()) {
            $this->deleteError = 'Kelas tidak bisa dihapus karena masih memiliki siswa aktif.';

            return;
        }

        $class?->delete();
        $this->deletingId = null;
        $this->deleteError = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'grade_level']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.guru-bk.school-class-management');
    }
}
