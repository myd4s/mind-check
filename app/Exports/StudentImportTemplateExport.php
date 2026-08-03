<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentImportTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Budi Santoso', 'budi.santoso@mindcheck.test', '2026010099', 'X IPA 1', 'L', '2009-05-14', '081234567890'],
        ];
    }

    public function headings(): array
    {
        return ['nama', 'email', 'nis', 'kelas', 'jenis_kelamin', 'tanggal_lahir', 'telepon'];
    }
}
