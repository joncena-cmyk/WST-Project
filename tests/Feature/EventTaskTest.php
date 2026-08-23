<?php

namespace Tests\Feature;

use App\Models\Committee;
use App\Models\Event;
use App\Models\EventTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTaskTest extends TestCase
{
    use RefreshDatabase;

    private function createEvent(User $user): Event
    {
        return Event::create([
            'title' => 'Web Dev Hackathon 2026',
            'description' => 'Hackathon for CCIS students.',
            'event_date' => now()->addDays(7)->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '18:00',
            'venue' => 'UMak Auditorium',
            'status' => Event::STATUS_PUBLISHED,
            'created_by' => $user->id,
        ]);
    }

    public function test_officer_can_create_task_for_event(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER, 'email_verified_at' => now(), 'account_status' => User::STATUS_ACTIVE]);
        $assignee = User::factory()->create(['role' => User::ROLE_MEMBER, 'email_verified_at' => now(), 'account_status' => User::STATUS_ACTIVE]);
        $event = $this->createEvent($officer);

        $committee = Committee::create([
            'event_id' => $event->id,
            'name' => 'Logistics',
            'created_by' => $officer->id,
        ]);

        $response = $this->actingAs($officer)->post(route('events.tasks.store', $event), [
            'title' => 'Prepare 50 extension cords',
            'description' => 'Ensure all cords are tested for electrical safety.',
            'committee_id' => $committee->id,
            'assigned_to' => $assignee->id,
            'due_date' => now()->addDays(3)->format('Y-m-d'),
            'priority' => 'high',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('event_tasks', [
            'event_id' => $event->id,
            'committee_id' => $committee->id,
            'title' => 'Prepare 50 extension cords',
            'assigned_to' => $assignee->id,
            'priority' => 'high',
            'status' => 'pending',
        ]);
    }

    public function test_assigned_user_can_view_and_update_task_status(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER, 'email_verified_at' => now(), 'account_status' => User::STATUS_ACTIVE]);
        $assignee = User::factory()->create(['role' => User::ROLE_STUDENT, 'email_verified_at' => now(), 'account_status' => User::STATUS_ACTIVE]);
        $event = $this->createEvent($officer);

        $task = EventTask::create([
            'event_id' => $event->id,
            'title' => 'Design social media posters',
            'assigned_to' => $assignee->id,
            'created_by' => $officer->id,
            'priority' => 'medium',
            'status' => EventTask::STATUS_PENDING,
        ]);

        // Assignee views "My Tasks"
        $viewResponse = $this->actingAs($assignee)->get(route('tasks.my-tasks'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Design social media posters');

        // Assignee updates status to in_progress
        $statusResponse = $this->actingAs($assignee)->post(route('tasks.status', $task), [
            'status' => 'in_progress',
        ]);
        $statusResponse->assertRedirect();
        $this->assertEquals('in_progress', $task->fresh()->status);

        // Assignee updates status to completed
        $completeResponse = $this->actingAs($assignee)->post(route('tasks.status', $task), [
            'status' => 'completed',
        ]);
        $completeResponse->assertRedirect();
        $this->assertEquals('completed', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
    }
}
