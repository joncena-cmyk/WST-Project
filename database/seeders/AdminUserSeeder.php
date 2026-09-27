<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $bscs = Program::where('code', 'BSCS')->first();
        $bsit = Program::where('code', 'BSIT')->first();
        $dit  = Program::where('code', 'DIT')->first();

        $defaultPassword = Hash::make('COMSOC2025!');

        // -----------------------------------------------------------------------
        // System Administrator & Adviser
        // -----------------------------------------------------------------------
        User::updateOrCreate(
            ['email' => 'admin@umak.edu.ph'],
            [
                'name'              => 'System Administrator',
                'password'          => Hash::make('Admin@COMSOC2025!'),
                'role'              => User::ROLE_ADMIN,
                'account_status'    => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'adviser@umak.edu.ph'],
            [
                'name'              => 'COMSOC Academic Adviser',
                'password'          => Hash::make('Adviser@COMSOC2025!'),
                'role'              => User::ROLE_ADVISER,
                'account_status'    => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // -----------------------------------------------------------------------
        // Organization Executive Board & Officers (Named Accounts)
        // -----------------------------------------------------------------------
        $officers = [
            // President
            [
                'name'       => 'Tristan Donne Brocales',
                'email'      => 'tristan.brocales@umak.edu.ph',
                'role'       => User::ROLE_PRESIDENT,
                'program_id' => $bscs?->id,
            ],
            // Vice-President (Internal)
            [
                'name'       => 'Jaisen Josh Laparan',
                'email'      => 'jaisen.laparan@umak.edu.ph',
                'role'       => User::ROLE_VP_INTERNAL,
                'program_id' => $bsit?->id,
            ],
            // Vice-President (External)
            [
                'name'       => 'Ethan Harvey De Ocampo',
                'email'      => 'ethan.deocampo@umak.edu.ph',
                'role'       => User::ROLE_VP_EXTERNAL,
                'program_id' => $bscs?->id,
            ],
            // Secretary
            [
                'name'       => 'Pauleen Mae Espenilla',
                'email'      => 'pauleen.espenilla@umak.edu.ph',
                'role'       => User::ROLE_SECRETARY,
                'program_id' => $bsit?->id,
            ],
            // Treasurer
            [
                'name'       => 'Jelden Joshua San Pedro',
                'email'      => 'jelden.sanpedro@umak.edu.ph',
                'role'       => User::ROLE_TREASURER,
                'program_id' => $bscs?->id,
            ],
            // Auditor
            [
                'name'       => 'Allyza Joy Alcovendas',
                'email'      => 'allyza.alcovendas@umak.edu.ph',
                'role'       => User::ROLE_AUDITOR,
                'program_id' => $bsit?->id,
            ],
            // PIO
            [
                'name'       => 'Jasper Barcenas',
                'email'      => 'jasper.barcenas@umak.edu.ph',
                'role'       => User::ROLE_PIO,
                'program_id' => $bsit?->id,
            ],
            // Computer Science Representative
            [
                'name'       => 'John Patrick Rongalerios',
                'email'      => 'johnpatrick.rongalerios@umak.edu.ph',
                'role'       => User::ROLE_PROGRAM_REP,
                'program_id' => $bscs?->id,
            ],
            // Information Technology Representative
            [
                'name'       => 'Charles Benedict Vega',
                'email'      => 'charles.vega@umak.edu.ph',
                'role'       => User::ROLE_PROGRAM_REP,
                'program_id' => $bsit?->id,
            ],
            // Diploma Representative
            [
                'name'       => 'Czybelle Ombao',
                'email'      => 'czybelle.ombao@umak.edu.ph',
                'role'       => User::ROLE_PROGRAM_REP,
                'program_id' => $dit?->id,
            ],
        ];

        foreach ($officers as $officer) {
            User::updateOrCreate(
                ['email' => $officer['email']],
                [
                    'name'              => $officer['name'],
                    'password'          => $defaultPassword,
                    'role'              => $officer['role'],
                    'program_id'        => $officer['program_id'],
                    'account_status'    => User::STATUS_ACTIVE,
                    'email_verified_at' => now(),
                ]
            );
        }

        // -----------------------------------------------------------------------
        // Role Alias Accounts (for convenience in testing & demos)
        // -----------------------------------------------------------------------
        $roleAliases = [
            'president@umak.edu.ph'   => ['name' => 'Tristan Donne Brocales (President)', 'role' => User::ROLE_PRESIDENT, 'program_id' => $bscs?->id],
            'vp.internal@umak.edu.ph' => ['name' => 'Jaisen Josh Laparan (VP Internal)', 'role' => User::ROLE_VP_INTERNAL, 'program_id' => $bsit?->id],
            'vp.external@umak.edu.ph' => ['name' => 'Ethan Harvey De Ocampo (VP External)', 'role' => User::ROLE_VP_EXTERNAL, 'program_id' => $bscs?->id],
            'secretary@umak.edu.ph'   => ['name' => 'Pauleen Mae Espenilla (Secretary)', 'role' => User::ROLE_SECRETARY, 'program_id' => $bsit?->id],
            'treasurer@umak.edu.ph'   => ['name' => 'Jelden Joshua San Pedro (Treasurer)', 'role' => User::ROLE_TREASURER, 'program_id' => $bscs?->id],
            'auditor@umak.edu.ph'     => ['name' => 'Allyza Joy Alcovendas (Auditor)', 'role' => User::ROLE_AUDITOR, 'program_id' => $bsit?->id],
            'pio@umak.edu.ph'         => ['name' => 'Jasper Barcenas (PIO)', 'role' => User::ROLE_PIO, 'program_id' => $bsit?->id],
            'cs.rep@umak.edu.ph'      => ['name' => 'John Patrick Rongalerios (CS Rep)', 'role' => User::ROLE_PROGRAM_REP, 'program_id' => $bscs?->id],
            'it.rep@umak.edu.ph'      => ['name' => 'Charles Benedict Vega (IT Rep)', 'role' => User::ROLE_PROGRAM_REP, 'program_id' => $bsit?->id],
            'diploma.rep@umak.edu.ph' => ['name' => 'Czybelle Ombao (Diploma Rep)', 'role' => User::ROLE_PROGRAM_REP, 'program_id' => $dit?->id],
        ];

        foreach ($roleAliases as $email => $data) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $data['name'],
                    'password'          => $defaultPassword,
                    'role'              => $data['role'],
                    'program_id'        => $data['program_id'],
                    'account_status'    => User::STATUS_ACTIVE,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
