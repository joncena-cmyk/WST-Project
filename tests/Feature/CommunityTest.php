<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_view_community(): void
    {
        $response = $this->get(route('community.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_community_feed(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'body'    => 'Welcome to the clean COMSOC Community Hub!',
            'type'    => Post::TYPE_COMMUNITY,
        ]);

        $response = $this->actingAs($user)->get(route('community.index'));

        $response->assertStatus(200);
        $response->assertSee('Welcome to the clean COMSOC Community Hub!');
        $response->assertSee('COMSOC Hub');
        $response->assertSee('Community Guidelines');
    }

    public function test_user_can_filter_community_posts_by_official_and_community(): void
    {
        $officer = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_PRESIDENT,
        ]);

        $student = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_STUDENT,
        ]);

        Post::create([
            'user_id' => $officer->id,
            'body'    => 'Official Announcement: General Assembly is set!',
            'type'    => Post::TYPE_OFFICIAL,
        ]);

        Post::create([
            'user_id' => $student->id,
            'body'    => 'Student discussion: Any tips for data structures?',
            'type'    => Post::TYPE_COMMUNITY,
        ]);

        // All posts
        $responseAll = $this->actingAs($student)->get(route('community.index'));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Official Announcement: General Assembly is set!');
        $responseAll->assertSee('Student discussion: Any tips for data structures?');

        // Official only
        $responseOfficial = $this->actingAs($student)->get(route('community.index', ['filter' => 'official']));
        $responseOfficial->assertStatus(200);
        $responseOfficial->assertSee('Official Announcement: General Assembly is set!');
        $responseOfficial->assertDontSee('Student discussion: Any tips for data structures?');

        // Community only
        $responseCommunity = $this->actingAs($student)->get(route('community.index', ['filter' => 'community']));
        $responseCommunity->assertStatus(200);
        $responseCommunity->assertDontSee('Official Announcement: General Assembly is set!');
        $responseCommunity->assertSee('Student discussion: Any tips for data structures?');
    }

    public function test_upcoming_event_spotlight_renders_in_community_hub(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
        ]);

        Event::create([
            'title'          => 'Hackathon 2026',
            'description'    => '48-hour build contest',
            'event_date'     => now()->addDays(3)->format('Y-m-d'),
            'start_time'     => '08:00',
            'end_time'       => '20:00',
            'venue'          => 'UMak Tech Lab',
            'status'         => Event::STATUS_PUBLISHED,
            'points_awarded' => 15,
            'created_by'     => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('community.index'));
        $response->assertStatus(200);
        $response->assertSee('Upcoming Event');
        $response->assertSee('Hackathon 2026');
    }
}
