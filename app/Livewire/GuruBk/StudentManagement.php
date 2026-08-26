<?php

namespace App\Livewire\GuruBk;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Exports\StudentImportTemplateExport;
use App\Imports\StudentsImport;
use App\Livewire\Concerns\WithTableControls;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class StudentManagement extends Component
{
    use WithFileUploads, WithPagination, WithTableControls;

    public ?int $editingId = null;

    public string $name = '';

    public string $nisn = '';

    public string $gender = '';

    public ?int $school_class_id = null;

    #[Url]
    public string $classFilter = '';

    public bool $showModal = false;

    public ?int $deactivatingId = null;

    public ?int $deletingId = null;

    public ?array $lastCreatedCredentials = null;

    public bool $showImportModal = false;

    public $importFile = null;

    public ?array $importResults = null;

    #[Computed]
    public function activeAcademicYear(): ?AcademicYear
    {
        return AcademicYear::where('is_active', true)->first();
    }

    #[Computed]
    public function schoolClasses()
    {
        return SchoolClass::orderBy('grade_level')->orderBy('name')->get();
    }

    #[Computed]
    public function students()
    {
        $activeYear = $this->activeAcademicYear;

        return Student::query()
            ->with(['user', 'currentClassHistory.schoolClass'])
            ->join('users', 'users.id', '=', 'students.user_id')
            // Join tahun ajaran aktif saja (bukan currentClassHistory relation) supaya
            // kelas bisa dipakai untuk filter & sort langsung di query pagination.
            // unique(student_id, academic_year_id) menjamin join ini tidak menggandakan baris.
            ->leftJoin('student_class_histories', function ($join) use ($activeYear) {
                $join->on('student_class_histories.student_id', '=', 'students.id')
                    ->where('student_class_histories.academic_year_id', $activeYear?->id);
            })
            ->leftJoin('school_classes', 'school_classes.id', '=', 'student_class_histories.school_class_id')
            ->when($this->search, fn ($query) => $query->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->search}%")
                    ->orWhere('students.nisn', 'like', "%{$this->search}%");
            }))
            ->when($this->classFilter, fn ($query) => $query->where('school_classes.id', $this->classFilter))
            ->orderBy($this->sortField ?: 'users.name', $this->sortField ? $this->sortDirection : 'asc')
            ->select('students.*')
            ->paginate($this->perPage);
    }

    public function updatingClassFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->lastCreatedCredentials = null;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $student = Student::with('currentClassHistory')->findOrFail($id);

        $this->editingId = $student->id;
        $this->name = $student->user->name;
        $this->nisn = $student->nisn;
        $this->gender = $student->gender->value;
        $this->school_class_id = $student->currentClassHistory?->school_class_id;
        $this->lastCreatedCredentials = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $activeYear = $this->activeAcademicYear;

        abort_unless($activeYear, 422, 'Tidak ada tahun ajaran aktif. Aktifkan salah satu tahun ajaran terlebih dahulu.');

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'nisn' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'nisn')->ignore($this->editingId),
            ],
            'gender' => ['required', Rule::enum(Gender::class)],
            'school_class_id' => 'required|exists:school_classes,id',
        ]);

        if ($this->editingId) {
            $student = Student::findOrFail($this->editingId);
            $student->user->update([
                'name' => $validated['name'],
                'email' => "{$validated['nisn']}@mindcare.com",
            ]);
            $student->update([
                'nisn' => $validated['nisn'],
                'gender' => $validated['gender'],
            ]);

            StudentClassHistory::updateOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $activeYear->id],
                ['school_class_id' => $validated['school_class_id'], 'status' => 'aktif']
            );
        } else {
            DB::transaction(function () use ($validated, $activeYear) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => "{$validated['nisn']}@mindcare.com",
                    'password' => $validated['nisn'],
                    'role' => UserRole::Siswa,
                    'must_change_password' => true,
                    'email_verified_at' => now(),
                ]);

                $student = Student::create([
                    'user_id' => $user->id,
                    'nisn' => $validated['nisn'],
                    'gender' => $validated['gender'],
                ]);

                StudentClassHistory::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $activeYear->id,
                    'school_class_id' => $validated['school_class_id'],
                    'status' => 'aktif',
                ]);

                $this->lastCreatedCredentials = [
                    'email' => $user->email,
                    'password' => $validated['nisn'],
                ];
            });
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDeactivate(int $id): void
    {
        $this->deactivatingId = $id;
    }

    public function deactivate(): void
    {
        $student = Student::with('currentClassHistory')->find($this->deactivatingId);

        $student?->currentClassHistory?->update(['status' => 'nonaktif']);

        $this->deactivatingId = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        $student = Student::with('user')->find($this->deletingId);

        // Hapus lewat User (bukan Student) supaya cascadeOnDelete FK ikut
        // membersihkan baris students, student_class_histories, dan
        // assessment_results — tanpa ini User akan jadi baris yatim.
        $student?->user?->delete();

        $this->deletingId = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentImportTemplateExport, 'template-import-siswa.xlsx');
    }

    public function openImportModal(): void
    {
        $this->importFile = null;
        $this->importResults = null;
        $this->resetErrorBag();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importResults = null;
    }

    public function import(): void
    {
        $activeYear = $this->activeAcademicYear;

        abort_unless($activeYear, 422, 'Tidak ada tahun ajaran aktif. Aktifkan salah satu tahun ajaran terlebih dahulu.');

        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new StudentsImport($activeYear);
        Excel::import($import, $this->importFile);

        $this->importResults = [
            'success' => $import->successCount,
            'error' => $import->errorCount,
            'rows' => $import->results,
        ];

        $this->importFile = null;
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'nisn', 'gender', 'school_class_id']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.guru-bk.student-management');
    }
}
