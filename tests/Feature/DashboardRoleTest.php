<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_sees_student_dashboard(): void
    {
        $student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'student@umak.edu.ph',
        ]);

        $response = $this->actingAs($student)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Student Dashboard');
        $response->assertSee('Registered');
    }

    public function test_officer_sees_officer_operations_dashboard(): void
    {
        $officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'officer@umak.edu.ph',
        ]);

        $response = $this->actingAs($officer)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Officer Operations Dashboard');
        $response->assertSee('Task Velocity');
    }

    public function test_adviser_sees_academic_adviser_console(): void
    {
        $adviser = User::factory()->create([
            'role'           => User::ROLE_ADVISER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'adviser@umak.edu.ph',
        ]);

        $response = $this->actingAs($adviser)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Academic Adviser Console');
        $response->assertSee('Approved Academic Organization Schedule');
    }

    public function test_admin_sees_administrator_console(): void
    {
        $admin = User::factory()->create([
            'role'           => User::ROLE_ADMIN,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'admin@umak.edu.ph',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('System Administrator Console');
        $response->assertSee('Total Registered Accounts');
    }

    public function test_all_org_officials_see_officer_operations_dashboard(): void
    {
        $roles = [
            User::ROLE_PRESIDENT,
            User::ROLE_VP_INTERNAL,
            User::ROLE_VP_EXTERNAL,
            User::ROLE_SECRETARY,
            User::ROLE_TREASURER,
            User::ROLE_AUDITOR,
            User::ROLE_PIO,
            User::ROLE_PROGRAM_REP,
        ];

        foreach ($roles as $role) {
            $official = User::factory()->create([
                'role'           => $role,
                'account_status' => User::STATUS_ACTIVE,
            ]);

            $this->assertTrue($official->isOfficerOrAbove());
            $this->assertTrue($official->isOfficer());

            $response = $this->actingAs($official)->get(route('dashboard'));
            $response->assertStatus(200);
            $response->assertSee('Officer Operations Dashboard');
        }
    }
}
