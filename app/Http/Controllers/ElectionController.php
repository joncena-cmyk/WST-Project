<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Election;
use App\Models\ElectionPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ElectionController extends Controller
{
    public function index(Request $request): View
    {
        $elections = Election::with(['creator', 'positions'])
            ->withCount(['approvedCandidates', 'voterParticipations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('elections.index', compact('elections'));
    }

    public function create(): View
    {
        Gate::authorize('create', Election::class);

        return view('elections.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Election::class);

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'academic_year'   => 'required|string|max:20',
            'filing_start_at' => 'nullable|date',
            'filing_end_at'   => 'nullable|date|after_or_equal:filing_start_at',
            'voting_start_at' => 'nullable|date',
            'voting_end_at'   => 'nullable|date|after_or_equal:voting_start_at',
            'positions'       => 'required|array|min:1',
            'positions.*'     => 'required',
        ]);

        $election = DB::transaction(function () use ($validated) {
            $election = Election::create([
                'title'           => $validated['title'],
                'description'     => $validated['description'] ?? null,
                'academic_year'   => $validated['academic_year'],
                'filing_start_at' => $validated['filing_start_at'] ?? null,
                'filing_end_at'   => $validated['filing_end_at'] ?? null,
                'voting_start_at' => $validated['voting_start_at'] ?? null,
                'voting_end_at'   => $validated['voting_end_at'] ?? null,
                'status'          => Election::STATUS_DRAFT,
                'created_by'      => Auth::id(),
            ]);

            foreach ($validated['positions'] as $idx => $posData) {
                if (is_array($posData)) {
                    $title = trim($posData['title'] ?? '');
                    $maxWinners = (int) ($posData['max_winners'] ?? 1);
                } else {
                    $title = trim((string) $posData);
                    $maxWinners = 1;
                }

                if ($title !== '') {
                    ElectionPosition::create([
                        'election_id' => $election->id,
                        'title'       => $title,
                        'order'       => $idx + 1,
                        'max_winners' => max(1, $maxWinners),
                    ]);
                }
            }

            AuditLog::record('election_created', "Created election '{$election->title}'.", $election);

            return $election;
        });

        return redirect()->route('elections.show', $election)
            ->with('success', 'Election created successfully in draft status.');
    }

    public function show(Election $election): View
    {
        $election->load([
            'positions.approvedCandidates.user.program',
            'creator',
        ]);

        $user = Auth::user();
        $hasVoted = $user ? $election->hasUserVoted($user) : false;
        $hasApplied = $user ? $election->hasUserApplied($user) : false;

        return view('elections.show', compact('election', 'hasVoted', 'hasApplied'));
    }

    public function edit(Election $election): View
    {
        Gate::authorize('update', $election);

        $election->load('positions');

        return view('elections.edit', compact('election'));
    }

    public function update(Request $request, Election $election): RedirectResponse
    {
        Gate::authorize('update', $election);

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'academic_year'   => 'required|string|max:20',
            'filing_start_at' => 'nullable|date',
            'filing_end_at'   => 'nullable|date|after_or_equal:filing_start_at',
            'voting_start_at' => 'nullable|date',
            'voting_end_at'   => 'nullable|date|after_or_equal:voting_start_at',
        ]);

        $election->update($validated);

        AuditLog::record('election_updated', "Updated election '{$election->title}'.", $election);

        return redirect()->route('elections.show', $election)
            ->with('success', 'Election details updated successfully.');
    }

    public function transitionStatus(Request $request, Election $election): RedirectResponse
    {
        Gate::authorize('update', $election);

        $validated = $request->validate([
            'status' => 'required|in:draft,filing_open,voting_open,closed,results_published',
        ]);

        $oldStatus = $election->status;
        $newStatus = $validated['status'];

        // Enforce state machine — prevent illegal jumps (e.g. draft → results_published)
        if (!$election->canTransitionTo($newStatus)) {
            return back()->with(
                'error',
                "Cannot transition from '{$election->getStatusLabel()}' to '{$newStatus}'. " .
                "Allowed next states: " . implode(', ', Election::VALID_TRANSITIONS[$oldStatus] ?? ['none']) . "."
            );
        }

        $election->update(['status' => $newStatus]);

        AuditLog::record(
            'election_status_changed',
            "Changed status of election '{$election->title}' from {$oldStatus} to {$newStatus}.",
            $election,
            ['old_status' => $oldStatus, 'new_status' => $newStatus]
        );

        return redirect()->route('elections.show', $election)
            ->with('success', "Election status updated to {$election->getStatusLabel()}.");
    }

    public function destroy(Election $election): RedirectResponse
    {
        Gate::authorize('delete', $election);

        $title = $election->title;
        $election->delete();

        AuditLog::record('election_deleted', "Deleted election '{$title}'.");

        return redirect()->route('elections.index')
            ->with('success', 'Election deleted successfully.');
    }
}
