<?php

namespace Tests\Feature;

use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class StudentsImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_valid_rows_and_skips_invalid_ones(): void
    {
        SchoolClass::create(['name' => 'X IPA 1']);

        $rows = new Collection([
            collect([
                'nama' => 'Siswa Satu',
                'email' => 'siswa1@mindcheck.test',
                'nis' => '2026010001',
                'kelas' => 'X IPA 1',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => null,
                'telepon' => null,
            ]),
            collect([
                'nama' => 'Siswa Dua',
                'email' => 'not-an-email',
                'nis' => '2026010002',
                'kelas' => 'X IPA 1',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => null,
                'telepon' => null,
            ]),
            collect([
                'nama' => 'Siswa Tiga',
                'email' => 'siswa3@mindcheck.test',
                'nis' => '2026010003',
                'kelas' => 'Kelas Tidak Ada',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => null,
                'telepon' => null,
            ]),
        ]);

        $import = new StudentsImport;
        $import->collection($rows);

        $this->assertSame(1, $import->created);
        $this->assertCount(2, $import->skipped);

        $this->assertDatabaseHas('users', ['email' => 'siswa1@mindcheck.test']);
        $this->assertDatabaseMissing('users', ['email' => 'siswa3@mindcheck.test']);

        $student = Student::where('nis', '2026010001')->firstOrFail();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('2026010001', $student->user->password));
    }
}
