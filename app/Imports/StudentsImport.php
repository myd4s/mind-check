<?php

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;

    /** @var array<int, string> */
    public array $skipped = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $data = [
                'nama' => trim((string) ($row['nama'] ?? '')),
                'email' => trim((string) ($row['email'] ?? '')),
                'nis' => trim((string) ($row['nis'] ?? '')),
                'kelas' => trim((string) ($row['kelas'] ?? '')),
                'jenis_kelamin' => strtoupper(trim((string) ($row['jenis_kelamin'] ?? ''))),
                'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                'telepon' => $row['telepon'] ?? null,
            ];

            $validator = Validator::make($data, [
                'nama' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'nis' => ['required', 'string', 'max:20', 'unique:students,nis'],
                'kelas' => ['required', 'string'],
                'jenis_kelamin' => ['required', 'in:L,P'],
            ]);

            if ($validator->fails()) {
                $this->skipped[] = "Baris {$rowNumber}: ".$validator->errors()->first();

                continue;
            }

            $schoolClass = SchoolClass::where('name', $data['kelas'])->first();

            if (! $schoolClass) {
                $this->skipped[] = "Baris {$rowNumber}: kelas '{$data['kelas']}' tidak ditemukan.";

                continue;
            }

            $user = User::create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => $data['nis'],
                'role' => UserRole::Student,
            ]);

            Student::create([
                'user_id' => $user->id,
                'class_id' => $schoolClass->id,
                'nis' => $data['nis'],
                'gender' => $data['jenis_kelamin'],
                'birth_date' => $data['tanggal_lahir'] ?: null,
                'phone' => $data['telepon'] ?: null,
                'status' => 'active',
            ]);

            $this->created++;
        }
    }
}
