<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_verify_authentic_certificate(): void
    {
        $officer = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_PRESIDENT,
        ]);

        $student = User::factory()->create([
            'name'              => 'Juan Dela Cruz',
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_STUDENT,
        ]);

        $event = Event::create([
            'title'          => 'Cybersecurity Summit 2026',
            'description'    => 'Annual cybersecurity conference',
            'event_date'     => now()->subDays(2)->format('Y-m-d'),
            'start_time'     => '09:00',
            'end_time'       => '17:00',
            'venue'          => 'UMak Auditorium',
            'status'         => Event::STATUS_COMPLETED,
            'points_awarded' => 10,
            'created_by'     => $officer->id,
        ]);

        $certificate = Certificate::create([
            'user_id'            => $student->id,
            'event_id'           => $event->id,
            'certificate_number' => 'COMSOC-2026-TEST1',
            'status'             => Certificate::STATUS_ISSUED,
            'issued_by'          => $officer->id,
            'issued_at'          => now(),
        ]);

        // Access as unauthenticated guest
        $response = $this->get(route('certificates.verify', 'COMSOC-2026-TEST1'));

        $response->assertStatus(200);
        $response->assertSee('Verified Authentic Certificate');
        $response->assertSee('Juan Dela Cruz');
        $response->assertSee('Cybersecurity Summit 2026');
        $response->assertSee('COMSOC-2026-TEST1');
    }

    public function test_public_verification_displays_revoked_status(): void
    {
        $officer = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_PRESIDENT,
        ]);

        $student = User::factory()->create([
            'name'              => 'Maria Santos',
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
        ]);

        $event = Event::create([
            'title'          => 'AI Bootcamp 2026',
            'description'    => 'Hands-on AI training',
            'event_date'     => now()->subDays(5)->format('Y-m-d'),
            'start_time'     => '09:00',
            'end_time'       => '17:00',
            'venue'          => 'Lab 1',
            'status'         => Event::STATUS_COMPLETED,
            'points_awarded' => 10,
            'created_by'     => $officer->id,
        ]);

        $certificate = Certificate::create([
            'user_id'            => $student->id,
            'event_id'           => $event->id,
            'certificate_number' => 'COMSOC-2026-REVOKED1',
            'status'             => Certificate::STATUS_REVOKED,
            'revoked_at'         => now(),
            'revocation_reason'  => 'Non-attendance at mandatory workshop hours.',
            'issued_by'          => $officer->id,
            'issued_at'          => now()->subDays(2),
        ]);

        $response = $this->get(route('certificates.verify', 'COMSOC-2026-REVOKED1'));

        $response->assertStatus(200);
        $response->assertSee('Certificate Revoked');
        $response->assertSee('Non-attendance at mandatory workshop hours.');
    }

    public function test_public_verification_displays_not_found_for_invalid_number(): void
    {
        $response = $this->get(route('certificates.verify', 'INVALID-CODE-999'));

        $response->assertStatus(200);
        $response->assertSee('Certificate Not Found');
        $response->assertSee('INVALID-CODE-999');
    }
}
