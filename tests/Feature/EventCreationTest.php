<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_can_create_event_with_standard_form_data(): void
    {
        $officer = User::factory()->create([
            'role' => User::ROLE_PRESIDENT,
            'email_verified_at' => now(),
            'account_status' => User::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($officer)->post(route('events.store'), [
            'title'                 => 'Tech Innovation Summit 2026',
            'description'           => 'A summit for CCIS students.',
            'event_date'            => '2026-08-26',
            'start_time'            => '09:05',
            'end_time'              => '17:05',
            'venue'                 => 'Auditorium',
            'registration_deadline' => '2026-08-25T05:06',
            'participant_limit'     => '',
            'requirements'          => ['Student ID required'],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('events.manage'));
        $this->assertDatabaseHas('events', [
            'title' => 'Tech Innovation Summit 2026',
            'venue' => 'Auditorium',
            'status' => Event::STATUS_DRAFT,
        ]);
    }
}
