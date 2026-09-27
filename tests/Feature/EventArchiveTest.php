<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventArchiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_upcoming_events_page_only_shows_future_events(): void
    {
        $officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
        ]);

        // Future upcoming event
        $futureEvent = Event::create([
            'title'                 => 'Future Tech Summit 2027',
            'description'           => 'Exciting upcoming conference.',
            'event_date'            => now()->addMonths(2)->toDateString(),
            'start_time'            => '09:00:00',
            'end_time'              => '17:00:00',
            'venue'                 => 'Main Auditorium',
            'registration_deadline' => now()->addMonth(),
            'status'                => Event::STATUS_PUBLISHED,
            'created_by'            => $officer->id,
        ]);

        // Past event
        $pastEvent = Event::create([
            'title'                 => 'Expired Past Hackathon 2025',
            'description'           => 'Concluded hackathon.',
            'event_date'            => now()->subMonths(3)->toDateString(),
            'start_time'            => '08:00:00',
            'end_time'              => '16:00:00',
            'venue'                 => 'Computer Lab 3',
            'registration_deadline' => now()->subMonths(4),
            'status'                => Event::STATUS_COMPLETED,
            'created_by'            => $officer->id,
        ]);

        $response = $this->actingAs($officer)->get(route('events.index'));

        $response->assertOk();
        $response->assertSee('Future Tech Summit 2027');
        $response->assertDontSee('Expired Past Hackathon 2025');
    }

    public function test_archive_page_displays_concluded_and_archived_events_with_statistics(): void
    {
        $officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
        ]);

        $student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
        ]);

        // Concluded event
        $concludedEvent = Event::create([
            'title'                 => 'Web Development Bootcamp 2026',
            'description'           => 'Hands-on web training session.',
            'event_date'            => now()->subWeeks(2)->toDateString(),
            'start_time'            => '10:00:00',
            'end_time'              => '15:00:00',
            'venue'                 => 'UMak AVR',
            'registration_deadline' => now()->subWeeks(3),
            'status'                => Event::STATUS_COMPLETED,
            'created_by'            => $officer->id,
        ]);

        // Add registrations, attendance, and certificates
        $registration = EventRegistration::create([
            'event_id'      => $concludedEvent->id,
            'user_id'       => $student->id,
            'status'        => 'registered',
            'registered_at' => now()->subWeeks(3),
        ]);

        EventAttendance::create([
            'event_id'          => $concludedEvent->id,
            'user_id'           => $student->id,
            'registration_id'   => $registration->id,
            'recorded_by'       => $officer->id,
            'attendance_status' => 'present',
            'checked_in_at'     => now()->subWeeks(2),
        ]);

        Certificate::create([
            'event_id'           => $concludedEvent->id,
            'user_id'            => $student->id,
            'certificate_number' => 'CERT-2026-TEST01',
            'status'             => 'issued',
            'issued_by'          => $officer->id,
            'issued_at'          => now()->subWeeks(2),
        ]);

        $response = $this->actingAs($officer)->get(route('events.archive'));

        $response->assertOk();
        $response->assertSee('Web Development Bootcamp 2026');
        $response->assertSee('UMak AVR');
        $response->assertSee('Turnout Rate');
        $response->assertSee('100%');
        $response->assertSee('1 attendees');
        $response->assertSee('1 issued');
    }

    public function test_archive_page_filters_by_search_and_year(): void
    {
        $officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
        ]);

        Event::create([
            'title'                 => 'Cybersecurity Symposium 2025',
            'description'           => 'Security best practices.',
            'event_date'            => '2025-05-15',
            'start_time'            => '09:00:00',
            'end_time'              => '12:00:00',
            'venue'                 => 'Auditorium A',
            'registration_deadline' => '2025-05-10',
            'status'                => Event::STATUS_COMPLETED,
            'created_by'            => $officer->id,
        ]);

        Event::create([
            'title'                 => 'AI and Data Summit 2026',
            'description'           => 'Machine learning advances.',
            'event_date'            => '2026-03-20',
            'start_time'            => '13:00:00',
            'end_time'              => '17:00:00',
            'venue'                 => 'Auditorium B',
            'registration_deadline' => '2026-03-15',
            'status'                => Event::STATUS_COMPLETED,
            'created_by'            => $officer->id,
        ]);

        // Search query
        $searchResponse = $this->actingAs($officer)->get(route('events.archive', ['search' => 'Cybersecurity']));
        $searchResponse->assertSee('Cybersecurity Symposium 2025');
        $searchResponse->assertDontSee('AI and Data Summit 2026');

        // Year filter
        $yearResponse = $this->actingAs($officer)->get(route('events.archive', ['year' => '2026']));
        $yearResponse->assertSee('AI and Data Summit 2026');
        $yearResponse->assertDontSee('Cybersecurity Symposium 2025');
    }

    public function test_officer_can_archive_completed_event(): void
    {
        $officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
        ]);

        $event = Event::create([
            'title'                 => 'Annual General Assembly',
            'description'           => 'All members meeting.',
            'event_date'            => now()->subDays(5)->toDateString(),
            'start_time'            => '09:00:00',
            'end_time'              => '12:00:00',
            'venue'                 => 'Grand Hall',
            'status'                => Event::STATUS_COMPLETED,
            'created_by'            => $officer->id,
        ]);

        $response = $this->actingAs($officer)->post(route('events.archive-event', $event));

        $response->assertRedirect(route('events.archive'));
        $this->assertEquals(Event::STATUS_ARCHIVED, $event->fresh()->status);
    }
}
