<?php

namespace App\Livewire\GuruBk;

use App\Livewire\Concerns\WithTableControls;
use App\Models\AcademicYear;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AcademicYearManagement extends Component
{
    use WithPagination, WithTableControls;

    public ?int $editingId = null;

    public string $name = '';

    public string $start_date = '';

    public string $end_date = '';

    public bool $is_active = false;

    public bool $showModal = false;

    public ?int $deletingId = null;

    #[Computed]
    public function academicYears()
    {
        return AcademicYear::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField ?: 'start_date', $this->sortField ? $this->sortDirection : 'desc')
            ->paginate($this->perPage);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $year = AcademicYear::findOrFail($id);

        $this->editingId = $year->id;
        $this->name = $year->name;
        $this->start_date = $year->start_date->format('Y-m-d');
        $this->end_date = $year->end_date->format('Y-m-d');
        $this->is_active = $year->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        AcademicYear::updateOrCreate(['id' => $this->editingId], $validated);

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        AcademicYear::find($this->deletingId)?->delete();
        $this->deletingId = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'start_date', 'end_date', 'is_active']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.guru-bk.academic-year-management');
    }
}
