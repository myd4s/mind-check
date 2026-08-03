<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            'X IPA 1', 'X IPA 2', 'X IPS 1',
            'XI IPA 1', 'XI IPA 2', 'XI IPS 1',
            'XII IPA 1', 'XII IPA 2', 'XII IPS 1',
        ])->each(fn (string $name) => SchoolClass::updateOrCreate(['name' => $name]));
    }
}
