<?php

namespace Tests\Feature\GuruBk;

use App\Enums\UserRole;
use App\Livewire\GuruBk\StudentManagement;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class StudentsImportTest extends TestCase
{
    use RefreshDatabase;

    private function activeAcademicYear(): AcademicYear
    {
        return AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);
    }

    private function makeUploadedSheet(array $rows): UploadedFile
    {
        $filename = 'import-test-'.uniqid().'.xlsx';

        Excel::store(new class($rows) implements FromArray, WithHeadings
        {
            public function __construct(private array $rows) {}

            public function array(): array
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return ['NISN', 'Nama', 'Jenis Kelamin', 'Kelas'];
            }
        }, $filename, 'local');

        $path = storage_path('app/private/'.$filename);

        if (! file_exists($path)) {
            $path = storage_path('app/'.$filename);
        }

        return \Illuminate\Http\Testing\File::createWithContent($filename, file_get_contents($path));
    }

    public function test_valid_rows_create_student_accounts(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $this->activeAcademicYear();
        SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);
        SchoolClass::create(['name' => 'X IPA 2', 'grade_level' => 'X']);

        $file = $this->makeUploadedSheet([
            ['1111111111', 'Siswa Satu', 'L', 'X IPA 1'],
            ['2222222222', 'Siswa Dua', 'P', 'X IPA 2'],
        ]);

        Livewire::actingAs($guruBk)
            ->test(StudentManagement::class)
            ->set('importFile', $file)
            ->call('import');

        $this->assertDatabaseHas('students', ['nisn' => '1111111111']);
        $this->assertDatabaseHas('students', ['nisn' => '2222222222']);
        $this->assertDatabaseHas('users', ['email' => '1111111111@mindcare.com']);
        $this->assertDatabaseHas('users', ['email' => '2222222222@mindcare.com']);
    }

    public function test_invalid_rows_are_reported_without_blocking_valid_rows(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $this->activeAcademicYear();
        SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        $existingUser = User::factory()->create(['role' => UserRole::Siswa]);
        Student::create(['user_id' => $existingUser->id, 'nisn' => '9999999999', 'gender' => 'L']);

        $file = $this->makeUploadedSheet([
            ['1111111111', 'Siswa Valid', 'L', 'X IPA 1'],
            ['9999999999', 'Siswa Duplikat', 'L', 'X IPA 1'],
            ['3333333333', 'Siswa Kelas Salah', 'P', 'Kelas Tidak Ada'],
            ['', 'Tanpa NISN', 'L', 'X IPA 1'],
        ]);

        $component = Livewire::actingAs($guruBk)
            ->test(StudentManagement::class)
            ->set('importFile', $file)
            ->call('import');

        $this->assertDatabaseHas('students', ['nisn' => '1111111111']);
        $this->assertDatabaseMissing('students', ['nisn' => '3333333333']);

        $results = $component->get('importResults');
        $this->assertSame(1, $results['success']);
        $this->assertSame(3, $results['error']);
    }

    public function test_cannot_import_without_active_academic_year(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        SchoolClass::create(['name' => 'X IPA 1', 'grade_level' => 'X']);

        $file = $this->makeUploadedSheet([
            ['1111111111', 'Siswa Satu', 'L', 'X IPA 1'],
        ]);

        Livewire::actingAs($guruBk)
            ->test(StudentManagement::class)
            ->set('importFile', $file)
            ->call('import')
            ->assertStatus(422);

        $this->assertDatabaseMissing('students', ['nisn' => '1111111111']);
    }

    public function test_siswa_cannot_access_import(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.students'))
            ->assertForbidden();
    }
}
