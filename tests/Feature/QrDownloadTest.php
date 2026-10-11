<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_student_can_download_qr_code_png(): void
    {
        $program = Program::create(['name' => 'BS Computer Science', 'code' => 'BSCS', 'short_name' => 'BSCS', 'is_active' => true]);
        $student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'program_id'     => $program->id,
            'account_status' => 'active',
            'student_id'     => 'K12345678',
        ]);

        $event = Event::create([
            'created_by' => $student->id,
            'title'      => 'COMSOC Tech Summit 2026',
            'description'=> 'Annual technology conference and workshop.',
            'event_date' => now()->addDays(7)->toDateString(),
            'start_time' => '09:00:00',
            'venue'      => 'UMak Grand Theater',
            'status'     => Event::STATUS_PUBLISHED,
        ]);

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id'  => $student->id,
            'status'   => EventRegistration::STATUS_REGISTERED,
            'qr_code'  => 'TEST_QR_TOKEN_ABC123',
        ]);

        $response = $this->actingAs($student)->get(route('attendance.qr.download', $event));

        $response->assertStatus(200);
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertNotEmpty($response->getContent());
    }

    public function test_unregistered_student_cannot_download_qr(): void
    {
        $program = Program::create(['name' => 'BS Computer Science', 'code' => 'BSCS', 'short_name' => 'BSCS', 'is_active' => true]);
        $student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'program_id'     => $program->id,
            'account_status' => 'active',
        ]);

        $event = Event::create([
            'created_by' => $student->id,
            'title'      => 'COMSOC Tech Summit 2026',
            'description'=> 'Annual technology conference and workshop.',
            'event_date' => now()->addDays(7)->toDateString(),
            'start_time' => '09:00:00',
            'venue'      => 'UMak Grand Theater',
            'status'     => Event::STATUS_PUBLISHED,
        ]);

        $response = $this->actingAs($student)->get(route('attendance.qr.download', $event));

        $response->assertStatus(404);
    }
}
