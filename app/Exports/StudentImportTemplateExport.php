<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentImportTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['1234567890', 'Contoh Nama Siswa', 'L', 'X IPA 1'],
        ];
    }

    public function headings(): array
    {
        return ['NISN', 'Nama', 'Jenis Kelamin', 'Kelas'];
    }
}
