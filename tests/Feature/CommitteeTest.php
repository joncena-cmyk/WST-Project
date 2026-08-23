<?php

namespace Tests\Feature;

use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommitteeTest extends TestCase
{
    use RefreshDatabase;

    private function createEvent(User $user): Event
    {
        return Event::create([
            'title' => 'CodeFest 2026',
            'description' => 'Annual programming event.',
            'event_date' => now()->addDays(10)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'venue' => 'CCIS Lab 1',
            'status' => Event::STATUS_PUBLISHED,
            'created_by' => $user->id,
        ]);
    }

    public function test_officer_can_create_committee_for_event(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER, 'email_verified_at' => now(), 'account_status' => User::STATUS_ACTIVE]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER, 'email_verified_at' => now(), 'account_status' => User::STATUS_ACTIVE]);
        $event = $this->createEvent($officer);

        $response = $this->actingAs($officer)->post(route('events.committees.store', $event), [
            'name' => 'Program & Technical',
            'description' => 'Handles sound, lights, and program flow.',
            'head_user_id' => $member->id,
        ]);

        $response->assertRedirect(route('events.committees.index', $event));
        $this->assertDatabaseHas('committees', [
            'event_id' => $event->id,
            'name' => 'Program & Technical',
            'head_user_id' => $member->id,
        ]);

        $committee = Committee::where('name', 'Program & Technical')->first();
        $this->assertDatabaseHas('committee_members', [
            'committee_id' => $committee->id,
            'user_id' => $member->id,
            'role_in_committee' => CommitteeMember::ROLE_HEAD,
        ]);
    }

    public function test_student_cannot_create_committee(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT, 'email_verified_at' => now(), 'account_status' => User::STATUS_ACTIVE]);
        $event = $this->createEvent($student);

        $response = $this->actingAs($student)->post(route('events.committees.store', $event), [
            'name' => 'Logistics',
        ]);

        $response->assertForbidden();
    }

    public function test_officer_can_add_and_remove_members_from_committee(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER, 'email_verified_at' => now(), 'account_status' => User::STATUS_ACTIVE]);
        $student = User::factory()->create(['role' => User::ROLE_STUDENT, 'email_verified_at' => now(), 'account_status' => User::STATUS_ACTIVE]);
        $event = $this->createEvent($officer);

        $committee = Committee::create([
            'event_id' => $event->id,
            'name' => 'Creatives & Marketing',
            'created_by' => $officer->id,
        ]);

        // Add member
        $response = $this->actingAs($officer)->post(route('committees.members.store', $committee), [
            'user_id' => $student->id,
            'role_in_committee' => 'member',
        ]);

        $response->assertRedirect(route('committees.show', $committee));
        $this->assertDatabaseHas('committee_members', [
            'committee_id' => $committee->id,
            'user_id' => $student->id,
            'role_in_committee' => 'member',
        ]);

        // Remove member
        $removeResponse = $this->actingAs($officer)->delete(route('committees.members.destroy', [$committee, $student]));
        $removeResponse->assertRedirect(route('committees.show', $committee));
        $this->assertDatabaseMissing('committee_members', [
            'committee_id' => $committee->id,
            'user_id' => $student->id,
        ]);
    }
}
