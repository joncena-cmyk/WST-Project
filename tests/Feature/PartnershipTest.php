<?php

namespace Tests\Feature;

use App\Models\CollaborationActivity;
use App\Models\PartnerOrganization;
use App\Models\Partnership;
use App\Models\PartnershipDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PartnershipTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private User $adviser;
    private User $student;
    private PartnerOrganization $partner;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'officer@umak.edu.ph',
        ]);

        $this->adviser = User::factory()->create([
            'role'           => User::ROLE_ADVISER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'adviser@umak.edu.ph',
        ]);

        $this->student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'student@umak.edu.ph',
        ]);

        $this->partner = PartnerOrganization::create([
            'name'   => 'Google Developer Student Clubs UMak',
            'type'   => PartnerOrganization::TYPE_STUDENT_ORG,
            'status' => PartnerOrganization::STATUS_ACTIVE,
        ]);
    }

    public function test_officer_can_propose_partnership(): void
    {
        $response = $this->actingAs($this->officer)->post(route('partnerships.store'), [
            'partner_id'    => $this->partner->id,
            'title'         => 'Cloud Study Jams 2026',
            'academic_year' => '2025-2026',
            'description'   => 'Joint tech study tracks.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('partnerships', [
            'title'      => 'Cloud Study Jams 2026',
            'partner_id' => $this->partner->id,
            'status'     => Partnership::STATUS_PROPOSED,
        ]);
    }

    public function test_adviser_can_approve_partnership_with_remarks(): void
    {
        $partnership = Partnership::create([
            'partner_id'    => $this->partner->id,
            'title'         => 'Cloud Study Jams 2026',
            'academic_year' => '2025-2026',
            'status'        => Partnership::STATUS_UNDER_REVIEW,
            'created_by'    => $this->officer->id,
        ]);

        $response = $this->actingAs($this->adviser)->post(route('partnerships.status', $partnership), [
            'status'  => Partnership::STATUS_APPROVED,
            'remarks' => 'Approved. Proceed with MOA signing.',
        ]);

        $response->assertRedirect();
        $this->assertEquals(Partnership::STATUS_APPROVED, $partnership->fresh()->status);
        $this->assertEquals('Approved. Proceed with MOA signing.', $partnership->fresh()->review_remarks);
    }

    public function test_officer_can_add_activity_and_upload_document(): void
    {
        $partnership = Partnership::create([
            'partner_id'    => $this->partner->id,
            'title'         => 'Cloud Study Jams 2026',
            'academic_year' => '2025-2026',
            'status'        => Partnership::STATUS_ACTIVE,
            'created_by'    => $this->officer->id,
        ]);

        // Add activity
        $actResponse = $this->actingAs($this->officer)->post(route('partnerships.activities.store', $partnership), [
            'title'         => 'Hands-on Cloud Run Workshop',
            'activity_type' => CollaborationActivity::TYPE_WORKSHOP,
            'activity_date' => now()->addDays(7)->toDateString(),
            'venue'         => 'CCIS Lab 301',
        ]);
        $actResponse->assertRedirect();
        $this->assertDatabaseHas('collaboration_activities', [
            'partnership_id' => $partnership->id,
            'title'          => 'Hands-on Cloud Run Workshop',
        ]);

        // Upload document
        $file = UploadedFile::fake()->create('signed_moa.pdf', 500, 'application/pdf');
        $docResponse = $this->actingAs($this->officer)->post(route('partnerships.documents.store', $partnership), [
            'title'         => 'Official Signed MOA',
            'document_type' => PartnershipDocument::TYPE_MOA_MOU,
            'file'          => $file,
        ]);
        $docResponse->assertRedirect();
        $this->assertDatabaseHas('partnership_documents', [
            'partnership_id' => $partnership->id,
            'title'          => 'Official Signed MOA',
        ]);
    }
}
