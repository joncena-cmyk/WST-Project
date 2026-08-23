<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_events_update_statuses_command(): void
    {
        $user = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'officer@umak.edu.ph',
        ]);

        // Event that should become ongoing
        $eventOngoing = Event::create([
            'title'                 => 'Live Tech Hackathon',
            'description'           => 'Happening today.',
            'event_date'            => now()->toDateString(),
            'start_time'            => '00:00:00',
            'end_time'              => '23:59:59',
            'venue'                 => 'Lab 1',
            'registration_deadline' => now()->subHour(),
            'status'                => Event::STATUS_PUBLISHED,
            'created_by'            => $user->id,
        ]);

        // Event that should become completed
        $eventCompleted = Event::create([
            'title'                 => 'Yesterday Workshop',
            'description'           => 'Already passed.',
            'event_date'            => now()->subDays(2)->toDateString(),
            'start_time'            => '09:00:00',
            'end_time'              => '12:00:00',
            'venue'                 => 'Room 201',
            'registration_deadline' => now()->subDays(3),
            'status'                => Event::STATUS_ONGOING,
            'created_by'            => $user->id,
        ]);

        $this->artisan('events:update-statuses')->assertSuccessful();

        $this->assertEquals(Event::STATUS_ONGOING, $eventOngoing->fresh()->status);
        $this->assertEquals(Event::STATUS_COMPLETED, $eventCompleted->fresh()->status);
    }

    public function test_memberships_expire_command(): void
    {
        $user = User::factory()->create([
            'role'           => User::ROLE_MEMBER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'member@umak.edu.ph',
        ]);

        $lapsedMembership = Membership::create([
            'user_id'       => $user->id,
            'academic_year' => '2024-2025',
            'status'        => Membership::STATUS_ACTIVE,
            'start_date'    => now()->subYears(2),
            'end_date'      => now()->subDay(),
        ]);

        $this->artisan('memberships:expire')->assertSuccessful();

        $this->assertEquals(Membership::STATUS_EXPIRED, $lapsedMembership->fresh()->status);
    }
}
