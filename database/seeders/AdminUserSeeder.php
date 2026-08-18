<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // System Administrator
        User::updateOrCreate(
            ['email' => 'admin@comsoc.edu.ph'],
            [
                'name'           => 'System Administrator',
                'password'       => Hash::make('Admin@COMSOC2025!'),
                'role'           => User::ROLE_ADMIN,
                'account_status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // Test Adviser account
        User::updateOrCreate(
            ['email' => 'adviser@comsoc.edu.ph'],
            [
                'name'           => 'COMSOC Adviser',
                'password'       => Hash::make('Adviser@COMSOC2025!'),
                'role'           => User::ROLE_ADVISER,
                'account_status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]
        );
    }
}
