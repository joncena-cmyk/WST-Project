<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = User::factory()->create([
            'role'           => User::ROLE_ADMIN,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'admin@umak.edu.ph',
        ]);

        AuditLog::record('test_action', 'This is a test audit event.', null, ['foo' => 'bar'], $admin);

        $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('This is a test audit event.');
    }

    public function test_student_cannot_view_audit_logs(): void
    {
        $student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'student@umak.edu.ph',
        ]);

        $response = $this->actingAs($student)->get(route('admin.audit-logs.index'));
        $response->assertStatus(403);
    }
}
