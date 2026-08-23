<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventPublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_can_publish_approved_event(): void
    {
        $officer = User::factory()->create([
            'role' => User::ROLE_PRESIDENT,
            'email_verified_at' => now(),
            'account_status' => User::STATUS_ACTIVE,
        ]);

        $event = Event::create([
            'title'                 => 'Tech Innovation Summit 2026',
            'description'           => 'A summit for CCIS students.',
            'event_date'            => '2026-08-26',
            'start_time'            => '09:05',
            'end_time'              => '17:05',
            'venue'                 => 'Auditorium',
            'registration_deadline' => '2026-08-25 05:06:00',
            'created_by'            => $officer->id,
            'status'                => Event::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($officer)
            ->from(route('events.show', $event))
            ->post(route('events.publish', $event));

        $response->assertRedirect(route('events.show', $event));
        $this->assertEquals(Event::STATUS_PUBLISHED, $event->fresh()->status);
    }
}
