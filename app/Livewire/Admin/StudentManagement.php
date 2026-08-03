<?php

namespace App\Livewire\Admin;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Imports\StudentsImport;
use App\Livewire\Forms\StudentForm;
use App\Models\SchoolClass;
use App\Models\Student;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class StudentManagement extends Component
{
    use WithFileUploads, WithPagination;

    public StudentForm $form;

    #[Url]
    public string $search = '';

    #[Url]
    public string $classFilter = '';

    #[Url]
    public string $genderFilter = '';

    #[Url]
    public string $statusFilter = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public ?int $viewingStudentId = null;

    public ?int $deletingStudentId = null;

    public $importFile = null;

    public ?string $importError = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
    }

    public function updatingGenderFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function students()
    {
        return Student::query()
            ->with(['user', 'schoolClass'])
            ->when($this->search, fn ($query) => $query->where(function ($q) {
                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"))
                    ->orWhere('nis', 'like', "%{$this->search}%");
            }))
            ->when($this->classFilter, fn ($query) => $query->where('class_id', $this->classFilter))
            ->when($this->genderFilter, fn ($query) => $query->where('gender', $this->genderFilter))
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->sortField === 'name', fn ($query) => $query->join('users', 'users.id', '=', 'students.user_id')
                ->orderBy('users.name', $this->sortDirection)
                ->select('students.*'))
            ->when($this->sortField !== 'name', fn ($query) => $query->orderBy($this->sortField, $this->sortDirection))
            ->paginate(10);
    }

    #[Computed]
    public function classes()
    {
        return SchoolClass::orderBy('name')->get();
    }

    #[Computed]
    public function viewingStudent(): ?Student
    {
        return $this->viewingStudentId
            ? Student::with(['user', 'schoolClass', 'assessments' => fn ($q) => $q->where('status', 'completed')->latest('completed_at')])->find($this->viewingStudentId)
            : null;
    }

    public function openCreateModal(): void
    {
        $this->form->resetForm();
        $this->dispatch('open-modal', 'student-form');
    }

    public function openEditModal(int $studentId): void
    {
        $this->form->resetForm();
        $this->form->setFromStudent(Student::with('user')->findOrFail($studentId));
        $this->dispatch('open-modal', 'student-form');
    }

    public function openViewModal(int $studentId): void
    {
        $this->viewingStudentId = $studentId;
        $this->dispatch('open-modal', 'student-view');
    }

    public function confirmDelete(int $studentId): void
    {
        $this->deletingStudentId = $studentId;
        $this->dispatch('open-modal', 'student-delete');
    }

    public function delete(): void
    {
        if ($this->deletingStudentId) {
            Student::findOrFail($this->deletingStudentId)->user->delete();
        }

        $this->deletingStudentId = null;
        $this->dispatch('close-modal', 'student-delete');
        session()->flash('status', 'Data siswa berhasil dihapus.');
    }

    public function save(): void
    {
        $this->form->save();

        $this->dispatch('close-modal', 'student-form');
        session()->flash('status', 'Data siswa berhasil disimpan.');
    }

    public function openImportModal(): void
    {
        $this->reset('importFile', 'importError');
        $this->dispatch('open-modal', 'student-import');
    }

    public function import(): void
    {
        $this->validate(['importFile' => 'required|mimes:xlsx,csv,txt']);

        $import = new StudentsImport;

        try {
            Excel::import($import, $this->importFile->getRealPath());
        } catch (\Throwable $e) {
            $this->importError = 'Gagal mengimpor: '.$e->getMessage();

            return;
        }

        $this->reset('importFile');
        $this->dispatch('close-modal', 'student-import');

        $message = "{$import->created} siswa berhasil diimpor.";

        if (! empty($import->skipped)) {
            $message .= ' '.count($import->skipped).' baris dilewati: '.implode('; ', array_slice($import->skipped, 0, 5));
        }

        session()->flash('status', $message);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.student-management', [
            'genders' => Gender::cases(),
            'statuses' => StudentStatus::cases(),
        ]);
    }
}
