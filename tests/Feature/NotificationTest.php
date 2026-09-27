<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\GeneralComsocNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_notifications_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
        ]);

        $user->notify(new GeneralComsocNotification(
            title: "Welcome to COMSOC!",
            message: "Your student portal is ready to use.",
            actionUrl: route('dashboard'),
            type: 'info'
        ));

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Welcome to COMSOC!');
        $response->assertSee('Your student portal is ready to use.');
    }

    public function test_user_can_fetch_quick_notifications_json(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
        ]);

        $user->notify(new GeneralComsocNotification(
            title: "Task Assigned",
            message: "New task assigned to you.",
            type: 'warning'
        ));

        $response = $this->actingAs($user)->getJson(route('notifications.quick'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unread_count',
            'notifications' => [
                '*' => ['id', 'title', 'message', 'type', 'is_read', 'created_at'],
            ],
        ]);
        $this->assertEquals(1, $response->json('unread_count'));
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
        ]);

        $user->notify(new GeneralComsocNotification(
            title: "Test Alert",
            message: "Test message.",
            type: 'info'
        ));

        $notification = $user->unreadNotifications()->first();
        $this->assertNotNull($notification);

        $response = $this->actingAs($user)->postJson(route('notifications.read', $notification->id));
        $response->assertStatus(200);

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
        ]);

        $user->notify(new GeneralComsocNotification(title: "Alert 1", message: "Msg 1"));
        $user->notify(new GeneralComsocNotification(title: "Alert 2", message: "Msg 2"));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->postJson(route('notifications.mark-all-read'));
        $response->assertStatus(200);

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }
}
