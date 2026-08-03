<?php

namespace App\Livewire\Forms;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class StudentForm extends Form
{
    public ?int $studentId = null;

    public string $name = '';

    public string $email = '';

    public string $nis = '';

    public ?int $class_id = null;

    public string $gender = 'L';

    public ?string $birth_date = null;

    public ?string $phone = null;

    public string $status = 'active';

    public function setFromStudent(Student $student): void
    {
        $this->studentId = $student->id;
        $this->name = $student->user->name;
        $this->email = $student->user->email;
        $this->nis = $student->nis;
        $this->class_id = $student->class_id;
        $this->gender = $student->gender->value;
        $this->birth_date = $student->birth_date?->format('Y-m-d');
        $this->phone = $student->phone;
        $this->status = $student->status->value;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->studentUserId())],
            'nis' => ['required', 'string', 'max:20', Rule::unique('students', 'nis')->ignore($this->studentId)],
            'class_id' => ['required', 'exists:classes,id'],
            'gender' => ['required', 'in:L,P'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    private function studentUserId(): ?int
    {
        return $this->studentId ? Student::find($this->studentId)?->user_id : null;
    }

    public function save(): Student
    {
        $this->validate();

        if ($this->studentId) {
            $student = Student::findOrFail($this->studentId);
            $student->user->update(['name' => $this->name, 'email' => $this->email]);
            $student->update([
                'class_id' => $this->class_id,
                'nis' => $this->nis,
                'gender' => $this->gender,
                'birth_date' => $this->birth_date,
                'phone' => $this->phone,
                'status' => $this->status,
            ]);

            return $student;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->nis,
            'role' => UserRole::Student,
        ]);

        return Student::create([
            'user_id' => $user->id,
            'class_id' => $this->class_id,
            'nis' => $this->nis,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'phone' => $this->phone,
            'status' => $this->status,
        ]);
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->gender = Gender::Male->value;
        $this->status = StudentStatus::Active->value;
    }
}
