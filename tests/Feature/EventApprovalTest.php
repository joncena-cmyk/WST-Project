<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $adviser;
    private User $officer;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adviser = User::factory()->create([
            'role'              => User::ROLE_ADVISER,
            'account_status'    => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $this->officer = User::factory()->create([
            'role'              => User::ROLE_PRESIDENT,
            'account_status'    => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $this->student = User::factory()->create([
            'role'              => User::ROLE_STUDENT,
            'account_status'    => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);
    }

    public function test_adviser_can_view_event_approval_queue(): void
    {
        $event = Event::create([
            'title'                 => 'Cybersecurity Hands-on Workshop',
            'description'           => 'Workshop on ethical hacking.',
            'event_date'            => now()->addDays(10)->toDateString(),
            'start_time'            => '09:00',
            'end_time'              => '17:00',
            'venue'                 => 'Lab 304',
            'registration_deadline' => now()->addDays(9)->toDateTimeString(),
            'created_by'            => $this->officer->id,
            'status'                => Event::STATUS_SUBMITTED,
        ]);

        $response = $this->actingAs($this->adviser)->get(route('events.approval.index'));
        $response->assertOk();
        $response->assertSee('Cybersecurity Hands-on Workshop');
    }

    public function test_adviser_can_approve_event(): void
    {
        $event = Event::create([
            'title'                 => 'AI & Machine Learning Bootcamp',
            'description'           => 'Bootcamp for beginners.',
            'event_date'            => now()->addDays(7)->toDateString(),
            'start_time'            => '08:00',
            'end_time'              => '16:00',
            'venue'                 => 'Grand Auditorium',
            'registration_deadline' => now()->addDays(6)->toDateTimeString(),
            'created_by'            => $this->officer->id,
            'status'                => Event::STATUS_SUBMITTED,
        ]);

        $response = $this->actingAs($this->adviser)->post(route('events.approval.review', $event), [
            'action'  => 'approved',
            'remarks' => 'Approved for student participation.',
        ]);

        $response->assertRedirect(route('events.approval.index'));
        $response->assertSessionHas('success', 'Event has been approved and is now authorized for publishing.');
        $this->assertEquals(Event::STATUS_APPROVED, $event->fresh()->status);
    }

    public function test_adviser_can_return_event_for_revisions(): void
    {
        $event = Event::create([
            'title'                 => 'Game Dev Hackathon',
            'description'           => '24-hour hackathon.',
            'event_date'            => now()->addDays(14)->toDateString(),
            'start_time'            => '09:00',
            'end_time'              => '17:00',
            'venue'                 => 'Room 501',
            'registration_deadline' => now()->addDays(13)->toDateTimeString(),
            'created_by'            => $this->officer->id,
            'status'                => Event::STATUS_SUBMITTED,
        ]);

        $response = $this->actingAs($this->adviser)->post(route('events.approval.review', $event), [
            'action'  => 'returned',
            'remarks' => 'Please provide more details on safety protocols and equipment needed.',
        ]);

        $response->assertRedirect(route('events.approval.index'));
        $response->assertSessionHas('success', 'Event has been returned for revisions.');
        $this->assertEquals(Event::STATUS_RETURNED, $event->fresh()->status);
    }

    public function test_student_cannot_review_events(): void
    {
        $event = Event::create([
            'title'        => 'Web Dev Basics',
            'description'  => 'Basics of HTML, CSS, and JS.',
            'event_date'   => now()->addDays(5)->toDateString(),
            'start_time'   => '09:00',
            'venue'        => 'Lab 101',
            'created_by'   => $this->officer->id,
            'status'       => Event::STATUS_SUBMITTED,
        ]);

        $response = $this->actingAs($this->student)->post(route('events.approval.review', $event), [
            'action' => 'approved',
        ]);

        $response->assertForbidden();
    }
}
