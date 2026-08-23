<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotingTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private User $voter;
    private User $candidateUser;
    private Election $election;
    private ElectionPosition $position;
    private Candidate $candidate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'officer@umak.edu.ph',
        ]);

        $this->voter = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'voter@umak.edu.ph',
        ]);

        $this->candidateUser = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'candidate@umak.edu.ph',
        ]);

        $this->election = Election::create([
            'title'         => 'COMSOC Board 2026',
            'academic_year' => '2025-2026',
            'status'        => Election::STATUS_VOTING_OPEN,
            'created_by'    => $this->officer->id,
        ]);

        $this->position = ElectionPosition::create([
            'election_id' => $this->election->id,
            'title'       => 'President',
            'order'       => 1,
            'max_winners' => 1,
        ]);

        $this->candidate = Candidate::create([
            'election_id' => $this->election->id,
            'position_id' => $this->position->id,
            'user_id'     => $this->candidateUser->id,
            'platform'    => 'Official platform',
            'status'      => Candidate::STATUS_APPROVED,
        ]);
    }

    public function test_eligible_voter_can_cast_secret_ballot(): void
    {
        $response = $this->actingAs($this->voter)->post(route('elections.vote.cast', $this->election), [
            'votes' => [
                $this->position->id => $this->candidate->id,
            ],
        ]);

        $response->assertRedirect(route('elections.show', $this->election));

        $this->assertTrue($this->election->hasUserVoted($this->voter));
        $this->assertDatabaseHas('voter_participations', [
            'election_id' => $this->election->id,
            'user_id'     => $this->voter->id,
        ]);
        $this->assertDatabaseHas('votes', [
            'election_id'  => $this->election->id,
            'position_id'  => $this->position->id,
            'candidate_id' => $this->candidate->id,
        ]);
    }

    public function test_voter_cannot_double_vote(): void
    {
        // First vote
        $this->actingAs($this->voter)->post(route('elections.vote.cast', $this->election), [
            'votes' => [
                $this->position->id => $this->candidate->id,
            ],
        ]);

        // Second vote attempt should be forbidden by policy
        $response = $this->actingAs($this->voter)->post(route('elections.vote.cast', $this->election), [
            'votes' => [
                $this->position->id => $this->candidate->id,
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_results_published_viewable_by_student(): void
    {
        $this->election->update(['status' => Election::STATUS_RESULTS_PUBLISHED]);

        $response = $this->actingAs($this->voter)->get(route('elections.results', $this->election));
        $response->assertStatus(200);
        $response->assertSee('Official Election Results');
        $response->assertSee($this->candidateUser->name);
    }
}
