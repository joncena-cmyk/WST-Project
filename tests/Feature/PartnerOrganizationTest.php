<?php

namespace Tests\Feature;

use App\Models\PartnerOrganization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerOrganizationTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'officer@umak.edu.ph',
        ]);

        $this->student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'student@umak.edu.ph',
        ]);
    }

    public function test_officer_can_create_partner_organization(): void
    {
        $response = $this->actingAs($this->officer)->post(route('partner-organizations.store'), [
            'name'           => 'AWS Cloud Club UMak',
            'type'           => PartnerOrganization::TYPE_STUDENT_ORG,
            'contact_person' => 'Juan Dela Cruz',
            'contact_email'  => 'aws@umak.edu.ph',
            'contact_phone'  => '+63 912 345 6789',
            'website'        => 'https://aws.amazon.com',
            'status'         => 'active',
        ]);

        $response->assertRedirect(route('partner-organizations.index'));
        $this->assertDatabaseHas('partner_organizations', [
            'name' => 'AWS Cloud Club UMak',
            'type' => 'student_org',
        ]);
    }

    public function test_student_cannot_create_partner_organization(): void
    {
        $response = $this->actingAs($this->student)->post(route('partner-organizations.store'), [
            'name'   => 'Unauthorized Tech Org',
            'type'   => 'industry',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }
}
