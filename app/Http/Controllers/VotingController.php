<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Membership;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoterParticipation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
     * Process secret ballot submission with cryptographic receipt generation
     */
    public function cast(Request $request, Election $election): RedirectResponse
    {
        Gate::authorize('vote', $election);

        $user = Auth::user();

        // Build valid position ID set for this election
        $positionIds = $election->positions->pluck('id')->all();

        // Validate selection per position
        $rules = [
            'votes' => ['required', 'array'],
        ];

        foreach ($election->positions as $pos) {
            if ($pos->max_winners === 1) {
                // Single-winner: nullable scalar candidate ID
                $rules["votes.{$pos->id}"] = [
                    'nullable',
                    Rule::exists('candidates', 'id')->where(function ($q) use ($election, $pos) {
                        $q->where('election_id', $election->id)
                          ->where('position_id', $pos->id)
                          ->where('status', Candidate::STATUS_APPROVED);
                    }),
                ];
            } else {
                // Multi-winner: array of candidate IDs, capped at max_winners
                $rules["votes.{$pos->id}"] = [
                    'nullable', 'array', "max:{$pos->max_winners}",
                ];
                $rules["votes.{$pos->id}.*"] = [
                    'integer',
                    Rule::exists('candidates', 'id')->where(function ($q) use ($election, $pos) {
                        $q->where('election_id', $election->id)
                          ->where('position_id', $pos->id)
                          ->where('status', Candidate::STATUS_APPROVED);
                    }),
                ];
            }
        }

        $validated = $request->validate($rules);

        // Strip any foreign position IDs from the submitted votes — only allow
        // positions that actually belong to this election (closes BUG-05 dead-variable gap).
        $validated['votes'] = array_intersect_key(
            $validated['votes'] ?? [],
            array_flip($positionIds)
        );

        $receiptData = DB::transaction(function () use ($election, $user, $validated) {
            // Acquire exclusive gap-lock on the unique index to prevent race conditions.
            $alreadyVoted = VoterParticipation::where('election_id', $election->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->exists();

            abort_if($alreadyVoted, 403, 'You have already cast your ballot in this election.');

            // Generate cryptographic receipt identifier and SHA-256 verification hash
            $receiptCode = 'VOTE-' . date('Y') . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
            $timestamp = now();
            $verificationHash = hash('sha256', $user->id . '|' . $election->id . '|' . $timestamp->toIso8601String() . '|' . config('app.key'));

            // 1. Record voter participation with cryptographic proof
            $participation = VoterParticipation::create([
                'election_id'       => $election->id,
                'user_id'           => $user->id,
                'receipt_code'      => $receiptCode,
                'verification_hash' => $verificationHash,
                'voted_at'          => $timestamp,
            ]);

            // 2. Cast individual anonymous secret ballot votes (separated from voter identity)
            foreach ($validated['votes'] as $positionId => $selection) {
                $candidateIds = is_array($selection) ? $selection : [$selection];
                foreach (array_filter($candidateIds) as $candidateId) {
                    Vote::create([
                        'election_id'  => $election->id,
                        'position_id'  => $positionId,
                        'candidate_id' => $candidateId,
                        'created_at'   => $timestamp,
                    ]);
                }
            }

            // 3. Record audit log
            AuditLog::record(
                'vote_cast',
                "Eligible voter {$user->name} cast official ballot [Receipt: {$receiptCode}] in election '{$election->title}'.",
                $election,
                ['receipt_code' => $receiptCode]
            );

            return [
                'receipt_code'      => $receiptCode,
                'verification_hash' => $verificationHash,
                'voted_at'          => $timestamp->format('M d, Y · h:i:s A T'),
                'election_title'    => $election->title,
                'academic_year'     => $election->academic_year,
            ];
        });

        return redirect()->route('elections.show', $election)
            ->with('success', 'Your official ballot has been cast and securely counted. Your digital voting receipt has been issued!')
            ->with('vote_receipt', $receiptData);
    }

    /**
     * View verifiable digital voting receipt
     */
    public function receipt(Election $election): View|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $participation = VoterParticipation::with('user')
            ->where('election_id', $election->id)
            ->where('user_id', $user->id)
            ->first();

        abort_unless($participation, 404, 'No voting record found for this election.');

        return view('elections.receipt', compact('election', 'participation'));
    }

    /**
     * View election results and live tallies
     */
    public function results(Election $election): View|RedirectResponse
    {
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

        // Count eligible voters: users with an active membership OR officers/advisers/admins
        $eligibleVoterCount = User::where('account_status', User::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereIn('role', array_merge(
                    User::OFFICER_ROLES,
                    [User::ROLE_ADVISER, User::ROLE_ADMIN]
                ))
                ->orWhereHas('memberships', fn($m) => $m->where('status', Membership::STATUS_ACTIVE));
            })
            ->count();

        // Calculate winners for each position with proper tie-handling.
        // Stable sort: primary = vote count desc, secondary = candidate ID asc (oldest first).
        // If candidates are tied exactly at the max_winners cutoff, all tied candidates are
        // flagged so the proclamation officer can decide; the automated proclaim() step
        // uses the same deterministic secondary sort.
        $winnersByPosition = [];
        foreach ($election->positions as $pos) {
            $sorted = $pos->approvedCandidates
                ->sortByDesc(fn($c) => $c->votes->count())
                ->values();

            // Determine the vote threshold at the max_winners boundary
            $cutoffCandidate = $sorted->get($pos->max_winners - 1);
            $cutoffVotes     = $cutoffCandidate ? $cutoffCandidate->votes->count() : 0;

            if ($cutoffVotes > 0) {
                // Include ALL candidates tied at the cutoff vote count
                $winnersByPosition[$pos->id] = $sorted->filter(
                    fn($c) => $c->votes->count() >= $cutoffVotes
                        && $sorted->search(fn($s) => $s->id === $c->id) < $pos->max_winners
                        || $c->votes->count() > $cutoffVotes
                );
            } else {
                $winnersByPosition[$pos->id] = collect();
            }
        }

        return view('elections.results', compact(
            'election',
            'totalBallotsCast',
            'eligibleVoterCount',
            'winnersByPosition'
        ));
    }

    /**
     * Official Proclamation & Automatic Role Transition for Elected Officers
     */
    public function proclaim(Request $request, Election $election): RedirectResponse
    {
        Gate::authorize('update', $election);

        abort_unless(
            $election->status === Election::STATUS_CLOSED,
            400,
            "Winners can only be proclaimed once, while the election is in 'Voting Closed' status. " .
            "Current status: '{$election->getStatusLabel()}'."
        );

        $election->load(['positions.approvedCandidates.user', 'positions.approvedCandidates.votes']);

        $promotedList = [];

        DB::transaction(function () use ($election, &$promotedList) {
            foreach ($election->positions as $pos) {
                // Stable sort: votes desc (primary), candidate ID asc (secondary tie-breaker)
                // sortKeys() was incorrect — it sorts by collection array-key, not candidate->id
                $sorted  = $pos->approvedCandidates
                    ->sortBy([
                        [fn($c) => $c->votes->count(), 'desc'],
                        [fn($c) => $c->id,             'asc'],
                    ])
                    ->values();
                $winners = $sorted->filter(fn($c) => $c->votes->count() > 0)->take($pos->max_winners);

                foreach ($winners as $winner) {
                    $targetRole = $this->mapPositionToRole($pos->title);
                    $winnerUser = $winner->user;

                    // Promote user account to the elected officer role
                    $winnerUser->update([
                        'role' => $targetRole,
                    ]);

                    $promotedList[] = "{$winnerUser->name} ({$pos->title} → " . (User::ROLES[$targetRole] ?? $targetRole) . ")";
                }
            }

            // Election ran once from STATUS_CLOSED, so this update always executes
            $election->update(['status' => Election::STATUS_RESULTS_PUBLISHED]);

            // Record immutable audit entry
            AuditLog::record(
                'election_winners_proclaimed',
                "Official proclamation executed by " . Auth::user()->name . " for '{$election->title}'. Newly sworn-in officers: " . implode('; ', $promotedList),
                $election,
                ['promoted_officers' => $promotedList]
            );
        });

        $count = count($promotedList);

        return redirect()->route('elections.results', $election)
            ->with('success', "🎉 Proclamation complete! {$count} elected officer account(s) have been officially promoted with executive portal privileges.");
    }

    /**
     * Helper to map an election position title to the appropriate User model role
     */
    private function mapPositionToRole(string $title): string
    {
        $clean = strtolower(trim($title));

        if (str_contains($clean, 'president') && !str_contains($clean, 'vice') && !str_contains($clean, 'vp')) {
            return User::ROLE_PRESIDENT;
        }
        if (str_contains($clean, 'internal')) {
            return User::ROLE_VP_INTERNAL;
        }
        if (str_contains($clean, 'external')) {
            return User::ROLE_VP_EXTERNAL;
        }
        if (str_contains($clean, 'secretary')) {
            return User::ROLE_SECRETARY;
        }
        if (str_contains($clean, 'treasurer')) {
            return User::ROLE_TREASURER;
        }
        if (str_contains($clean, 'auditor')) {
            return User::ROLE_AUDITOR;
        }
        if (str_contains($clean, 'pio') || str_contains($clean, 'public info') || str_contains($clean, 'public relations') || str_contains($clean, 'pro')) {
            return User::ROLE_PIO;
        }
        if (str_contains($clean, 'representative') || str_contains($clean, 'rep')) {
            return User::ROLE_PROGRAM_REP;
        }

        return User::ROLE_OFFICER;
    }
}
