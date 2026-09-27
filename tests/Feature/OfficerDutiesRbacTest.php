<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Document;
use App\Models\Event;
use App\Models\Membership;
use App\Models\PartnerOrganization;
use App\Models\Partnership;
use App\Models\Post;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficerDutiesRbacTest extends TestCase
{
    use RefreshDatabase;

    private Program $bscs;
    private Program $bsit;
    private Program $dit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bscs = Program::create(['name' => 'BS Computer Science', 'code' => 'BSCS', 'short_name' => 'CS', 'is_active' => true]);
        $this->bsit = Program::create(['name' => 'BS Information Technology', 'code' => 'BSIT', 'short_name' => 'IT', 'is_active' => true]);
        $this->dit  = Program::create(['name' => 'Diploma in IT', 'code' => 'DIT', 'short_name' => 'Diploma', 'is_active' => true]);
    }

    public function test_vp_external_sees_partnerships_console_and_can_create_partner(): void
    {
        $vpExternal = User::factory()->create([
            'role'           => User::ROLE_VP_EXTERNAL,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'ethan.deocampo@umak.edu.ph',
        ]);

        $response = $this->actingAs($vpExternal)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('VP External Desk');
        $response->assertSee('External Relations Overview');

        $orgResponse = $this->actingAs($vpExternal)->post(route('partner-organizations.store'), [
            'name'           => 'Acme Tech Solutions',
            'type'           => 'industry',
            'contact_person' => 'Jane Doe',
            'contact_email'  => 'jane@acme.com',
            'status'         => 'active',
        ]);

        $orgResponse->assertRedirect(route('partner-organizations.index'));
        $this->assertDatabaseHas('partner_organizations', ['name' => 'Acme Tech Solutions']);
    }

    public function test_treasurer_sees_financial_desk_and_can_manage_memberships(): void
    {
        $treasurer = User::factory()->create([
            'role'           => User::ROLE_TREASURER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'jelden.sanpedro@umak.edu.ph',
        ]);

        $response = $this->actingAs($treasurer)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Treasurer Desk');
        $response->assertSee('Membership Dues');

        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $membershipResponse = $this->actingAs($treasurer)->post(route('admin.memberships.store'), [
            'user_id'       => $student->id,
            'program_id'    => $this->bscs->id,
            'academic_year' => '2025-2026',
            'status'        => Membership::STATUS_ACTIVE,
        ]);

        $membershipResponse->assertRedirect(route('admin.memberships.index'));
        $this->assertDatabaseHas('memberships', [
            'user_id'       => $student->id,
            'academic_year' => '2025-2026',
            'status'        => Membership::STATUS_ACTIVE,
            'approved_by'   => $treasurer->id,
        ]);
    }

    public function test_auditor_can_inspect_audit_logs(): void
    {
        $auditor = User::factory()->create([
            'role'           => User::ROLE_AUDITOR,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'allyza.alcovendas@umak.edu.ph',
        ]);

        AuditLog::create([
            'user_id'     => $auditor->id,
            'action'      => 'system_check',
            'description' => 'Auditor routine inspection check',
            'ip_address'  => '127.0.0.1',
        ]);

        $response = $this->actingAs($auditor)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Auditor Desk');

        $auditResponse = $this->actingAs($auditor)->get(route('admin.audit-logs.index'));
        $auditResponse->assertStatus(200);
        $auditResponse->assertSee('Auditor routine inspection check');
    }

    public function test_student_cannot_access_audit_logs(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $response = $this->actingAs($student)->get(route('admin.audit-logs.index'));
        $response->assertStatus(403);
    }

    public function test_secretary_sees_records_desk_and_documents(): void
    {
        $secretary = User::factory()->create([
            'role'           => User::ROLE_SECRETARY,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'pauleen.espenilla@umak.edu.ph',
        ]);

        $response = $this->actingAs($secretary)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Secretary Desk');
        $response->assertSee('Records & Resolutions Registry');
    }

    public function test_pio_sees_communication_desk_and_moderation(): void
    {
        $pio = User::factory()->create([
            'role'           => User::ROLE_PIO,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'jasper.barcenas@umak.edu.ph',
        ]);

        $response = $this->actingAs($pio)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('PIO Desk');
        $response->assertSee('Communications & Moderation Center');

        $modResponse = $this->actingAs($pio)->get(route('moderation.index'));
        $modResponse->assertStatus(200);
    }

    public function test_program_rep_sees_cohort_information(): void
    {
        $csRep = User::factory()->create([
            'role'           => User::ROLE_PROGRAM_REP,
            'program_id'     => $this->bscs->id,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'johnpatrick.rongalerios@umak.edu.ph',
        ]);

        $response = $this->actingAs($csRep)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Program Representative Desk');
        $response->assertSee('CS Cohort');
    }
}
