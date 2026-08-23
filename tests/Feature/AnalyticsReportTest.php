<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsReportTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private User $student;
    private Event $event;

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

        $this->event = Event::create([
            'title'                 => 'Tech Innovation Summit 2026',
            'description'           => 'A summit for CCIS students.',
            'event_date'            => '2026-08-26',
            'start_time'            => '09:00',
            'end_time'              => '17:00',
            'venue'                 => 'Auditorium',
            'registration_deadline' => '2026-08-25 23:59:00',
            'created_by'            => $this->officer->id,
            'status'                => Event::STATUS_PUBLISHED,
        ]);
    }

    public function test_officer_can_view_analytics_hub(): void
    {
        $response = $this->actingAs($this->officer)->get(route('analytics.index'));
        $response->assertStatus(200);
        $response->assertSee('Organization Reports & Analytics');
        $response->assertSee('Event Turnout Rate');
    }

    public function test_student_cannot_view_analytics_hub(): void
    {
        $response = $this->actingAs($this->student)->get(route('analytics.index'));
        $response->assertStatus(403);
    }

    public function test_officer_can_export_attendance_csv(): void
    {
        $reg = EventRegistration::create([
            'event_id'   => $this->event->id,
            'user_id'    => $this->student->id,
            'status'     => EventRegistration::STATUS_ATTENDED,
            'qr_code'    => EventRegistration::generateQrCode(),
        ]);

        EventAttendance::create([
            'event_id'          => $this->event->id,
            'user_id'           => $this->student->id,
            'registration_id'   => $reg->id,
            'recorded_by'       => $this->officer->id,
            'checked_in_at'     => now(),
            'attendance_status' => EventAttendance::STATUS_PRESENT,
        ]);

        $response = $this->actingAs($this->officer)->get(route('analytics.export.attendance', $this->event));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_officer_can_export_memberships_csv(): void
    {
        Membership::create([
            'user_id'       => $this->student->id,
            'academic_year' => '2025-2026',
            'status'        => Membership::STATUS_ACTIVE,
            'start_date'    => now(),
            'end_date'      => now()->addYear(),
        ]);

        $response = $this->actingAs($this->officer)->get(route('analytics.export.memberships'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_officer_can_export_events_summary_csv(): void
    {
        $response = $this->actingAs($this->officer)->get(route('analytics.export.events'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
