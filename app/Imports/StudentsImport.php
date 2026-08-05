<?php

namespace App\Imports;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public array $results = [];

    public int $successCount = 0;

    public int $errorCount = 0;

    public function __construct(private readonly AcademicYear $activeAcademicYear) {}

    public function collection(Collection $rows): void
    {
        $seenNisns = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // baris 1 = header

            $nisn = trim((string) ($row['nisn'] ?? ''));
            $name = trim((string) ($row['nama'] ?? ''));
            $genderRaw = trim((string) ($row['jenis_kelamin'] ?? ''));
            $className = trim((string) ($row['kelas'] ?? ''));

            if ($nisn === '' && $name === '' && $genderRaw === '' && $className === '') {
                continue;
            }

            $errors = [];

            if ($nisn === '') {
                $errors[] = 'NISN wajib diisi.';
            } elseif (isset($seenNisns[$nisn])) {
                $errors[] = "NISN duplikat dengan baris {$seenNisns[$nisn]} pada file ini.";
            } elseif (Student::where('nisn', $nisn)->exists()) {
                $errors[] = 'NISN sudah terdaftar.';
            }

            if ($name === '') {
                $errors[] = 'Nama wajib diisi.';
            }

            $gender = $this->resolveGender($genderRaw);
            if (! $gender) {
                $errors[] = 'Jenis kelamin harus L/Laki-laki atau P/Perempuan.';
            }

            $schoolClass = $className !== '' ? SchoolClass::where('name', $className)->first() : null;
            if (! $schoolClass) {
                $errors[] = "Kelas '{$className}' tidak ditemukan.";
            }

            if (! empty($errors)) {
                $this->errorCount++;
                $this->results[] = [
                    'row' => $rowNumber,
                    'nisn' => $nisn,
                    'status' => 'error',
                    'message' => implode(' ', $errors),
                ];

                continue;
            }

            $seenNisns[$nisn] = $rowNumber;

            DB::transaction(function () use ($nisn, $name, $gender, $schoolClass) {
                $user = User::create([
                    'name' => $name,
                    'email' => "{$nisn}@mindcheck.com",
                    'password' => $nisn,
                    'role' => UserRole::Siswa,
                    'must_change_password' => true,
                    'email_verified_at' => now(),
                ]);

                $student = Student::create([
                    'user_id' => $user->id,
                    'nisn' => $nisn,
                    'gender' => $gender,
                ]);

                StudentClassHistory::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $this->activeAcademicYear->id,
                    'school_class_id' => $schoolClass->id,
                    'status' => 'aktif',
                ]);
            });

            $this->successCount++;
            $this->results[] = [
                'row' => $rowNumber,
                'nisn' => $nisn,
                'status' => 'success',
                'message' => 'Berhasil dibuat.',
            ];
        }
    }

    private function resolveGender(string $raw): ?Gender
    {
        $normalized = strtoupper($raw);

        return match (true) {
            in_array($normalized, ['L', 'LAKI-LAKI', 'LAKI LAKI'], true) => Gender::Male,
            in_array($normalized, ['P', 'PEREMPUAN'], true) => Gender::Female,
            default => null,
        };
    }
}
