<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Election;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CandidateController extends Controller
{
    /**
     * Candidacy review queue for officers & advisers
     */
    public function index(Request $request): View
    {
        Gate::authorize('review', Candidate::class);

        $query = Candidate::with(['user.program', 'election', 'position', 'reviewer'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('election_id')) {
            $query->where('election_id', $request->election_id);
        }

        $candidates = $query->paginate(15)->withQueryString();
        $elections = Election::orderBy('created_at', 'desc')->get();

        return view('elections.candidates.index', compact('candidates', 'elections'));
    }

    /**
     * Submit candidacy application
     */
    public function store(Request $request, Election $election): RedirectResponse
    {
        Gate::authorize('apply', [Candidate::class, $election]);

        $validated = $request->validate([
            'position_id' => 'required|exists:election_positions,id',
            'platform'    => 'required|string|max:3000',
            'party_name'  => 'nullable|string|max:100',
        ]);

        $candidate = Candidate::create([
            'election_id' => $election->id,
            'position_id' => $validated['position_id'],
            'user_id'     => Auth::id(),
            'platform'    => $validated['platform'],
            'party_name'  => $validated['party_name'] ?? null,
            'status'      => Candidate::STATUS_PENDING,
        ]);

        AuditLog::record(
            'candidate_applied',
            "User " . Auth::user()->name . " filed candidacy for position ID {$candidate->position_id} in election '{$election->title}'.",
            $candidate
        );

        return redirect()->route('elections.show', $election)
            ->with('success', 'Your candidacy application has been filed and submitted for officer/adviser review.');
    }

    /**
     * Review candidacy application (approve/reject)
     */
    public function review(Request $request, Candidate $candidate): RedirectResponse
    {
        Gate::authorize('review', Candidate::class);

        $validated = $request->validate([
            'decision' => 'required|in:approved,rejected',
            'remarks'  => 'nullable|string|max:1000',
        ]);

        $candidate->update([
            'status'         => $validated['decision'],
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
            'review_remarks' => $validated['remarks'] ?? null,
        ]);

        AuditLog::record(
            'candidate_reviewed',
            "Candidacy of {$candidate->user->name} was {$validated['decision']} by " . Auth::user()->name . ".",
            $candidate,
            ['decision' => $validated['decision'], 'remarks' => $validated['remarks'] ?? null]
        );

        return redirect()->back()
            ->with('success', "Candidate {$candidate->user->name} has been {$validated['decision']}.");
    }
}
