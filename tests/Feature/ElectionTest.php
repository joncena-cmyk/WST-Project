<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElectionTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'officer@umak.edu.ph',
        ]);

        $this->student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'student@umak.edu.ph',
        ]);
    }

    public function test_officer_can_create_election_with_positions(): void
    {
        $response = $this->actingAs($this->officer)->post(route('elections.store'), [
            'title'         => 'COMSOC Executive Board 2026',
            'academic_year' => '2025-2026',
            'description'   => 'General elections.',
            'positions'     => ['President', 'Vice President', 'Treasurer'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('elections', ['title' => 'COMSOC Executive Board 2026']);
        $this->assertDatabaseHas('election_positions', ['title' => 'President']);
        $this->assertDatabaseHas('election_positions', ['title' => 'Treasurer']);
    }

    public function test_student_can_apply_for_candidacy_when_filing_is_open(): void
    {
        $election = Election::create([
            'title'         => 'COMSOC Board 2026',
            'academic_year' => '2025-2026',
            'status'        => Election::STATUS_FILING_OPEN,
            'created_by'    => $this->officer->id,
        ]);

        $pos = ElectionPosition::create([
            'election_id' => $election->id,
            'title'       => 'President',
            'order'       => 1,
        ]);

        $response = $this->actingAs($this->student)->post(route('elections.apply', $election), [
            'position_id' => $pos->id,
            'party_name'  => 'Innovators Slate',
            'platform'    => 'My mission is to advance technology education.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('candidates', [
            'election_id' => $election->id,
            'user_id'     => $this->student->id,
            'status'      => Candidate::STATUS_PENDING,
        ]);
    }

    public function test_officer_can_approve_candidate(): void
    {
        $election = Election::create([
            'title'         => 'COMSOC Board 2026',
            'academic_year' => '2025-2026',
            'status'        => Election::STATUS_FILING_OPEN,
            'created_by'    => $this->officer->id,
        ]);

        $pos = ElectionPosition::create([
            'election_id' => $election->id,
            'title'       => 'President',
            'order'       => 1,
        ]);

        $cand = Candidate::create([
            'election_id' => $election->id,
            'position_id' => $pos->id,
            'user_id'     => $this->student->id,
            'platform'    => 'Student leadership platform',
            'status'      => Candidate::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->officer)->post(route('candidates.review', $cand), [
            'decision' => 'approved',
            'remarks'  => 'Eligible active student member in good standing.',
        ]);

        $response->assertRedirect();
        $this->assertEquals(Candidate::STATUS_APPROVED, $cand->fresh()->status);
    }
}
