<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\PartnerOrganization;
use App\Models\Partnership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FluidCollaborationPipelineTest extends TestCase
{
    use RefreshDatabase;

    private User $vpExternal;
    private User $adviser;
    private User $student;
    private PartnerOrganization $partner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vpExternal = User::factory()->create([
            'role'           => User::ROLE_VP_EXTERNAL,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'vpexternal@umak.edu.ph',
        ]);

        $this->adviser = User::factory()->create([
            'role'           => User::ROLE_ADVISER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'adviser@umak.edu.ph',
        ]);

        $this->student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'student1@umak.edu.ph',
        ]);

        $this->partner = PartnerOrganization::create([
            'name'           => 'AWS Cloud Club - University of Makati',
            'type'           => PartnerOrganization::TYPE_STUDENT_ORG,
            'contact_person' => 'John Doe',
            'contact_email'  => 'awsclub@umak.edu.ph',
            'status'         => PartnerOrganization::STATUS_ACTIVE,
        ]);
    }

    public function test_complete_fluid_collaboration_and_event_pipeline(): void
    {
        // -------------------------------------------------------------
        // Stage 1: VP External creates partnership proposal
        // -------------------------------------------------------------
        $createResponse = $this->actingAs($this->vpExternal)->post(route('partnerships.store'), [
            'partner_id'    => $this->partner->id,
            'title'         => 'Serverless Cloud Computing Bootcamp 2026',
            'description'   => 'Joint hands-on AWS serverless architecture training for CCIS students.',
            'academic_year' => '2025-2026',
            'start_date'    => now()->addDays(14)->toDateString(),
            'end_date'      => now()->addDays(15)->toDateString(),
        ]);

        $createResponse->assertRedirect();
        $partnership = Partnership::where('title', 'Serverless Cloud Computing Bootcamp 2026')->first();
        $this->assertNotNull($partnership);
        $this->assertEquals(Partnership::STATUS_PROPOSED, $partnership->status);

        // -------------------------------------------------------------
        // Stage 2: Draft and save formal Letter of Intent & Support Fund
        // -------------------------------------------------------------
        $letterResponse = $this->actingAs($this->vpExternal)->post(route('partnerships.save-letter', $partnership), [
            'sponsorship_amount' => 15000.00,
            'letter_content'     => "Dear AWS Cloud Club Leaders,\n\nCOMSOC formally submits this Letter of Intent for our joint bootcamp.",
        ]);

        $letterResponse->assertRedirect();
        $partnership->refresh();
        $this->assertEquals('15000.00', $partnership->sponsorship_amount);
        $this->assertStringContainsString('AWS Cloud Club Leaders', $partnership->letter_content);
        $this->assertNotNull($partnership->proposal_document_id);

        // -------------------------------------------------------------
        // Stage 3: Submit to Academic Adviser for review
        // -------------------------------------------------------------
        $submitResponse = $this->actingAs($this->vpExternal)->post(route('partnerships.submit-to-adviser', $partnership));
        $submitResponse->assertRedirect();
        $partnership->refresh();
        $this->assertEquals(Partnership::STATUS_UNDER_REVIEW, $partnership->status);

        // -------------------------------------------------------------
        // Stage 4: Academic Adviser reviews & endorses
        // -------------------------------------------------------------
        $endorseResponse = $this->actingAs($this->adviser)->post(route('partnerships.endorse', $partnership), [
            'remarks' => 'Endorsed. Great initiative for student cloud competency. Proceed with joint event setup.',
        ]);

        $endorseResponse->assertRedirect();
        $partnership->refresh();
        $this->assertEquals(Partnership::STATUS_APPROVED, $partnership->status);
        $this->assertEquals($this->adviser->id, $partnership->endorsed_by);
        $this->assertNotNull($partnership->endorsed_at);
        $this->assertTrue($partnership->isEndorsed());
        $this->assertTrue($partnership->canBeSpawnedToEvent());

        // -------------------------------------------------------------
        // Stage 5: 1-Click Convert to Joint COMSOC Event
        // -------------------------------------------------------------
        $spawnResponse = $this->actingAs($this->vpExternal)->post(route('partnerships.spawn-event', $partnership), [
            'title'                 => 'COMSOC x AWS: Serverless Cloud Computing Bootcamp 2026',
            'description'           => 'Official joint event with AWS Cloud Club UMak.',
            'event_date'            => now()->addDays(14)->toDateString(),
            'start_time'            => '09:00',
            'end_time'              => '17:00',
            'venue'                 => 'CCIS Computer Lab 304',
            'participant_limit'     => 100,
            'registration_deadline' => now()->addDays(13)->setTime(23, 59, 0)->toDateTimeString(),
        ]);

        $spawnResponse->assertRedirect();
        $partnership->refresh();
        $this->assertEquals(Partnership::STATUS_ACTIVE, $partnership->status);
        $this->assertNotNull($partnership->event_id);

        $event = Event::find($partnership->event_id);
        $this->assertNotNull($event);
        $this->assertTrue($event->is_collaboration);
        $this->assertEquals($this->partner->id, $event->partner_id);
        $this->assertEquals($partnership->id, $event->partnership_id);

        // Verify automated committee and task creation
        $this->assertDatabaseHas('committees', [
            'event_id' => $event->id,
            'name'     => 'Logistics & Facilities',
        ]);
        $this->assertDatabaseHas('committees', [
            'event_id' => $event->id,
            'name'     => 'Programs & Technical Support',
        ]);
        $this->assertDatabaseHas('committees', [
            'event_id' => $event->id,
            'name'     => 'Creatives & Promotions',
        ]);
        $this->assertDatabaseHas('event_tasks', [
            'event_id' => $event->id,
            'title'    => 'Secure University Facility Permit & Audio-Visual Equipment',
        ]);

        // -------------------------------------------------------------
        // Stage 6: Student Registers & Receives QR Pass
        // -------------------------------------------------------------
        $regResponse = $this->actingAs($this->student)->post(route('events.register', $event));
        $regResponse->assertRedirect();
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id'  => $this->student->id,
            'status'   => 'registered',
        ]);

        $passResponse = $this->actingAs($this->student)->get(route('attendance.pass', $event));
        $passResponse->assertOk();

        // -------------------------------------------------------------
        // Stage 7: Event Day Check-in via QR Scan
        // -------------------------------------------------------------
        $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $this->student->id)->first();
        $this->assertNotNull($registration);

        $checkInResponse = $this->actingAs($this->vpExternal)->post(route('attendance.checkin', $event), [
            'qr_code' => $registration->qr_code,
        ]);
        $checkInResponse->assertJson(['success' => true]);
        $this->assertDatabaseHas('event_attendance', [
            'event_id' => $event->id,
            'user_id'  => $this->student->id,
        ]);

        // -------------------------------------------------------------
        // Stage 8: Certificate Issuance & Public Verification
        // -------------------------------------------------------------
        $certResponse = $this->actingAs($this->vpExternal)->post(route('certificates.issue', $event), [
            'user_id' => $this->student->id,
        ]);
        $certResponse->assertRedirect();

        $cert = Certificate::where('event_id', $event->id)->where('user_id', $this->student->id)->first();
        $this->assertNotNull($cert);

        // Test public verification portal
        $verifyResponse = $this->get(route('certificates.verify', $cert->certificate_number));
        $verifyResponse->assertOk();
        $verifyResponse->assertSee($this->student->name);
        $verifyResponse->assertSee($event->title);
    }

    public function test_inbound_technical_support_partner_workflow(): void
    {
        // External org requests COMSOC to be the Technical Partner for their University Summit
        $response = $this->actingAs($this->vpExternal)->post(route('partnerships.store'), [
            'partner_id'       => $this->partner->id,
            'partnership_type' => Partnership::TYPE_TECHNICAL_SUPPORT,
            'title'            => 'UMak Annual Tech Summit 2026',
            'description'      => 'COMSOC requested to lead technical stage management, live streaming, and AV operations.',
            'academic_year'    => '2025-2026',
            'sponsorship_amount' => 5000.00,
        ]);

        $response->assertRedirect();
        $partnership = Partnership::where('title', 'UMak Annual Tech Summit 2026')->first();
        $this->assertNotNull($partnership);
        $this->assertEquals(Partnership::TYPE_TECHNICAL_SUPPORT, $partnership->partnership_type);
        $this->assertEquals('Technical Support & Operations Partner', $partnership->getPartnershipTypeLabel());
        $this->assertStringContainsString('Official Technical Partner', $partnership->getDefaultLetterContent());

        // Adviser Endorsement
        $this->actingAs($this->adviser)->post(route('partnerships.endorse', $partnership), [
            'remarks' => 'Endorsed. Assign Technical Committee officers to manage AV & Zoom broadcast.',
        ])->assertRedirect();

        // Spawn event with automated technical operations delegation
        $this->actingAs($this->vpExternal)->post(route('partnerships.spawn-event', $partnership), [
            'title'      => 'UMak Annual Tech Summit 2026 (Technical Ops by COMSOC)',
            'event_date' => now()->addDays(10)->toDateString(),
            'start_time' => '08:00',
            'venue'      => 'University Grand Auditorium',
        ])->assertRedirect();

        $event = $partnership->fresh()->event;
        $this->assertNotNull($event);

        // Verify technical operations committees & tasks were generated
        $this->assertDatabaseHas('committees', [
            'event_id' => $event->id,
            'name'     => 'Technical Operations & Audio-Visual',
        ]);
        $this->assertDatabaseHas('committees', [
            'event_id' => $event->id,
            'name'     => 'Technical Secretariat & Stage Cueing',
        ]);
        $this->assertDatabaseHas('event_tasks', [
            'event_id' => $event->id,
            'title'    => 'Setup Stage Audio-Visual, Microphones & Live Streaming Equipment',
        ]);
    }

    public function test_partnership_proposal_email_is_dispatched_to_partner_org(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        // 1. Automatic email dispatch upon creating partnership
        $response = $this->actingAs($this->vpExternal)->post(route('partnerships.store'), [
            'partner_id'       => $this->partner->id,
            'partnership_type' => Partnership::TYPE_CO_ORGANIZER,
            'title'            => 'AI Innovation Summit 2026',
            'description'      => 'Exploring Generative AI with partner cloud clubs.',
            'academic_year'    => '2025-2026',
            'sponsorship_amount' => 10000.00,
        ]);

        $response->assertRedirect();
        $partnership = Partnership::where('title', 'AI Innovation Summit 2026')->first();
        $this->assertNotNull($partnership);

        // Assert mail was sent to partner's contact_email
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\PartnershipProposalMail::class, function ($mail) use ($partnership) {
            return $mail->hasTo($this->partner->contact_email)
                && $mail->partnership->id === $partnership->id;
        });

        // 2. Manual 1-click email dispatch / re-send with custom message
        $customNote = 'Hi John, please review our initial draft. Looking forward to our sync meeting this Friday!';
        $sendResponse = $this->actingAs($this->vpExternal)->post(route('partnerships.send-email', $partnership), [
            'recipient_email' => 'partnerships-lead@umak.edu.ph',
            'custom_message'  => $customNote,
        ]);

        $sendResponse->assertRedirect();
        $sendResponse->assertSessionHas('success');

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\PartnershipProposalMail::class, function ($mail) use ($partnership, $customNote) {
            return $mail->hasTo('partnerships-lead@umak.edu.ph')
                && $mail->partnership->id === $partnership->id
                && $mail->customMessage === $customNote;
        });
    }

    public function test_partner_organization_can_accept_and_sign_proposal_via_public_portal(): void
    {
        $partnership = Partnership::create([
            'partner_id'         => $this->partner->id,
            'partnership_type'   => Partnership::TYPE_CO_ORGANIZER,
            'title'              => 'Web3 & Blockchain Development Workshop',
            'academic_year'      => '2025-2026',
            'status'             => Partnership::STATUS_PROPOSED,
            'created_by'         => $this->vpExternal->id,
            'sponsorship_amount' => 8000.00,
        ]);

        $this->assertNotEmpty($partnership->response_token);

        // 1. Partner accesses public portal via token without logging in
        $portalResponse = $this->get(route('partnerships.respond', [
            'partnership' => $partnership->id,
            'token'       => $partnership->response_token,
        ]));

        $portalResponse->assertOk();
        $portalResponse->assertSee('Web3 & Blockchain Development Workshop');
        $portalResponse->assertSee('Official Response &amp; Letter Confirmation', false);

        // 2. Access with invalid token is blocked
        $invalidResponse = $this->get(route('partnerships.respond', [
            'partnership' => $partnership->id,
            'token'       => 'invalid-token-12345',
        ]));
        $invalidResponse->assertNotFound();

        // 3. Partner submits official acceptance & confirmation
        $submitResponse = $this->post(route('partnerships.respond.submit', [
            'partnership' => $partnership->id,
            'token'       => $partnership->response_token,
        ]), [
            'response_status' => 'accepted',
            'responder_name'  => 'John Doe',
            'responder_title' => 'President, AWS Cloud Club',
            'remarks'         => 'We gladly accept this partnership proposal and look forward to co-organizing the event.',
        ]);

        $submitResponse->assertRedirect();
        $submitResponse->assertSessionHas('success');

        $partnership->refresh();
        $this->assertEquals(Partnership::RESPONSE_ACCEPTED, $partnership->partner_response_status);
        $this->assertEquals('John Doe', $partnership->partner_responder_name);
        $this->assertEquals('President, AWS Cloud Club', $partnership->partner_responder_title);
        $this->assertEquals(Partnership::STATUS_UNDER_REVIEW, $partnership->status);
        $this->assertTrue($partnership->isPartnerAccepted());
        $this->assertNotNull($partnership->partner_responded_at);
    }
}
