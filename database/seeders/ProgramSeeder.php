<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'name'       => 'Bachelor of Science in Computer Science',
                'code'       => 'BSCS',
                'short_name' => 'Computer Science',
                'is_active'  => true,
            ],
            [
                'name'       => 'Bachelor of Science in Information Technology',
                'code'       => 'BSIT',
                'short_name' => 'Information Technology',
                'is_active'  => true,
            ],
            [
                'name'       => 'Diploma in Information Technology',
                'code'       => 'DIT',
                'short_name' => 'Diploma',
                'is_active'  => true,
            ],
            [
                'name'       => 'Bachelor of Science in Information Systems',
                'code'       => 'BSIS',
                'short_name' => 'Information Systems',
                'is_active'  => true,
            ],
        ];

        foreach ($programs as $program) {
            Program::updateOrCreate(
                ['code' => $program['code']],
                $program
            );
        }
    }
}
