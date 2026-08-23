<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Election;
use App\Models\Vote;
use App\Models\VoterParticipation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VotingController extends Controller
{
    /**
     * Display the official secret ballot voting booth
     */
    public function show(Election $election): View|RedirectResponse
    {
        Gate::authorize('vote', $election);

        $election->load(['positions.approvedCandidates.user.program']);

        return view('elections.vote', compact('election'));
    }

    /**
     * Process secret ballot submission
     */
    public function cast(Request $request, Election $election): RedirectResponse
    {
        Gate::authorize('vote', $election);

        $user = Auth::user();

        // Validate selection per position
        $rules = [];
        foreach ($election->positions as $pos) {
            $rules["votes.{$pos->id}"] = 'nullable|exists:candidates,id';
        }

        $validated = $request->validate([
            'votes'   => 'required|array',
            ...$rules,
        ]);

        DB::transaction(function () use ($election, $user, $validated) {
            // Salted one-way voter hash for vote rows (cannot be reversed to identify user)
            $voterHash = hash('sha256', $user->id . '_' . $election->id . '_' . config('app.key'));

            // 1. Record voter participation to prevent multiple voting
            VoterParticipation::create([
                'election_id' => $election->id,
                'user_id'     => $user->id,
                'voted_at'    => now(),
            ]);

            // 2. Cast individual anonymous secret ballot votes
            foreach ($validated['votes'] as $positionId => $candidateId) {
                if ($candidateId) {
                    Vote::create([
                        'election_id'  => $election->id,
                        'position_id'  => $positionId,
                        'candidate_id' => $candidateId,
                        'voter_hash'   => $voterHash,
                        'created_at'   => now(),
                    ]);
                }
            }

            // 3. Record audit event
            AuditLog::record(
                'vote_cast',
                "Eligible voter {$user->name} cast their official ballot in election '{$election->title}'.",
                $election
            );
        });

        return redirect()->route('elections.show', $election)
            ->with('success', 'Your official ballot has been cast and securely counted. Thank you for voting!');
    }

    /**
     * View election results and live tallies
     */
    public function results(Election $election): View|RedirectResponse
    {
        // Allow viewing if results published, or if user is officer/adviser/admin
        $canViewDraft = Auth::check() && (Auth::user()->isOfficerOrAbove() || Auth::user()->isAdviser() || Auth::user()->isAdmin());

        abort_unless(
            $election->areResultsPublished() || $canViewDraft,
            403,
            'Official results have not been published yet.'
        );

        $election->load([
            'positions.approvedCandidates.user.program',
            'positions.approvedCandidates.votes',
        ]);

        $totalBallotsCast = $election->voterParticipations()->count();

        return view('elections.results', compact('election', 'totalBallotsCast'));
    }
}
