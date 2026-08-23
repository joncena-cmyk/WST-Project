<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\Event;
use App\Models\EventTask;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CommitteeController extends Controller
{
    /** Display committees and overall task overview for a specific event */
    public function index(Event $event): View
    {
        $this->authorize('viewAny', Committee::class);

        $event->load(['committees.head', 'committees.members.user', 'committees.tasks', 'tasks.assignee']);

        $totalTasks = $event->tasks->count();
        $completedTasks = $event->tasks->where('status', EventTask::STATUS_COMPLETED)->count();
        $inProgressTasks = $event->tasks->where('status', EventTask::STATUS_IN_PROGRESS)->count();
        $pendingTasks = $event->tasks->where('status', EventTask::STATUS_PENDING)->count();
        $overallProgress = $totalTasks > 0 ? (int) round(($completedTasks / $totalTasks) * 100) : 0;

        // Fetch active users for assignment options
        $activeUsers = User::where('account_status', User::STATUS_ACTIVE)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return view('committees.index', compact(
            'event',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'pendingTasks',
            'overallProgress',
            'activeUsers'
        ));
    }

    /** Store a newly created committee for an event */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('create', Committee::class);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'head_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $committee = $event->committees()->create([
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'head_user_id' => $validated['head_user_id'] ?? null,
            'created_by'   => Auth::id(),
        ]);

        // If a head user was selected, add them as a member with 'head' role
        if (!empty($validated['head_user_id'])) {
            CommitteeMember::updateOrCreate(
                ['committee_id' => $committee->id, 'user_id' => $validated['head_user_id']],
                ['role_in_committee' => CommitteeMember::ROLE_HEAD]
            );
        }

        return redirect()->route('events.committees.index', $event)
            ->with('success', "Committee '{$committee->name}' created successfully.");
    }

    /** Display the specified committee workspace */
    public function show(Committee $committee): View
    {
        $this->authorize('view', $committee);

        $committee->load([
            'event',
            'head',
            'creator',
            'members.user.program',
            'tasks.assignee',
            'tasks.creator'
        ]);

        $memberIds = $committee->members->pluck('user_id')->toArray();
        if ($committee->head_user_id) {
            $memberIds[] = $committee->head_user_id;
        }

        $availableUsers = User::where('account_status', User::STATUS_ACTIVE)
            ->whereNotIn('id', array_unique($memberIds))
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $committeeUsers = $committee->users()->get(['users.id', 'users.name', 'users.email', 'users.role']);
        if ($committee->head && !$committeeUsers->contains('id', $committee->head->id)) {
            $committeeUsers->push($committee->head);
        }

        return view('committees.show', compact('committee', 'availableUsers', 'committeeUsers'));
    }

    /** Update the specified committee */
    public function update(Request $request, Committee $committee): RedirectResponse
    {
        $this->authorize('update', $committee);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'head_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $previousHeadId = $committee->head_user_id;

        $committee->update([
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'head_user_id' => $validated['head_user_id'] ?? null,
        ]);

        // If head changed, register new head in committee_members
        if (!empty($validated['head_user_id']) && $validated['head_user_id'] != $previousHeadId) {
            CommitteeMember::updateOrCreate(
                ['committee_id' => $committee->id, 'user_id' => $validated['head_user_id']],
                ['role_in_committee' => CommitteeMember::ROLE_HEAD]
            );
        }

        return redirect()->route('committees.show', $committee)
            ->with('success', 'Committee details updated successfully.');
    }

    /** Remove the specified committee */
    public function destroy(Committee $committee): RedirectResponse
    {
        $this->authorize('delete', $committee);

        $event = $committee->event;
        $name = $committee->name;
        $committee->delete();

        return redirect()->route('events.committees.index', $event)
            ->with('success', "Committee '{$name}' deleted successfully.");
    }

    /** Add a member to the committee */
    public function addMember(Request $request, Committee $committee): RedirectResponse
    {
        $this->authorize('manageMembers', $committee);

        $validated = $request->validate([
            'user_id'           => ['required', 'exists:users,id'],
            'role_in_committee' => ['required', 'in:head,assistant,member'],
        ]);

        CommitteeMember::updateOrCreate(
            ['committee_id' => $committee->id, 'user_id' => $validated['user_id']],
            ['role_in_committee' => $validated['role_in_committee']]
        );

        if ($validated['role_in_committee'] === CommitteeMember::ROLE_HEAD) {
            $committee->update(['head_user_id' => $validated['user_id']]);
        }

        return redirect()->route('committees.show', $committee)
            ->with('success', 'Member added to committee successfully.');
    }

    /** Remove a member from the committee */
    public function removeMember(Committee $committee, User $user): RedirectResponse
    {
        $this->authorize('manageMembers', $committee);

        CommitteeMember::where('committee_id', $committee->id)
            ->where('user_id', $user->id)
            ->delete();

        if ($committee->head_user_id === $user->id) {
            $committee->update(['head_user_id' => null]);
        }

        return redirect()->route('committees.show', $committee)
            ->with('success', "{$user->name} removed from committee.");
    }
}
