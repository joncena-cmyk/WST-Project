<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\Event;
use App\Models\EventTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventTaskController extends Controller
{
    /** Personal task dashboard for the authenticated user across all events */
    public function myTasks(Request $request): View
    {
        $user = Auth::user();

        $query = EventTask::with(['event', 'committee', 'creator'])
            ->where('assigned_to', $user->id)
            ->latest('due_date');

        // Status filtering
        $status = $request->query('status');
        if ($status && in_array($status, [EventTask::STATUS_PENDING, EventTask::STATUS_IN_PROGRESS, EventTask::STATUS_COMPLETED])) {
            $query->where('status', $status);
        }

        $tasks = $query->paginate(12)->withQueryString();

        // Metrics
        $totalAssigned   = EventTask::where('assigned_to', $user->id)->count();
        $pendingCount    = EventTask::where('assigned_to', $user->id)->where('status', EventTask::STATUS_PENDING)->count();
        $inProgressCount = EventTask::where('assigned_to', $user->id)->where('status', EventTask::STATUS_IN_PROGRESS)->count();
        $completedCount  = EventTask::where('assigned_to', $user->id)->where('status', EventTask::STATUS_COMPLETED)->count();
        
        $overdueCount = EventTask::where('assigned_to', $user->id)
            ->whereNotIn('status', [EventTask::STATUS_COMPLETED, EventTask::STATUS_CANCELLED])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        return view('tasks.my-tasks', compact(
            'tasks',
            'status',
            'totalAssigned',
            'pendingCount',
            'inProgressCount',
            'completedCount',
            'overdueCount'
        ));
    }

    /** Event-wide task board for organizers and committee heads */
    public function eventTasks(Request $request, Event $event): View
    {
        $this->authorize('viewAny', Committee::class);

        $event->load(['committees']);

        $query = EventTask::with(['committee', 'assignee', 'creator'])
            ->where('event_id', $event->id);

        if ($request->filled('committee_id')) {
            $query->where('committee_id', $request->query('committee_id'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->query('assigned_to'));
        }

        $tasks = $query->latest('due_date')->paginate(20)->withQueryString();

        $activeUsers = User::where('account_status', User::STATUS_ACTIVE)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $committees = $event->committees;

        $stats = [
            'total'       => $event->tasks()->count(),
            'pending'     => $event->tasks()->where('status', EventTask::STATUS_PENDING)->count(),
            'in_progress' => $event->tasks()->where('status', EventTask::STATUS_IN_PROGRESS)->count(),
            'completed'   => $event->tasks()->where('status', EventTask::STATUS_COMPLETED)->count(),
        ];

        return view('tasks.event-tasks', compact('event', 'tasks', 'committees', 'activeUsers', 'stats'));
    }

    /** Store a new task for an event */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('create', EventTask::class);

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'committee_id' => ['nullable', 'exists:committees,id'],
            'assigned_to'  => ['nullable', 'exists:users,id'],
            'due_date'     => ['nullable', 'date'],
            'priority'     => ['required', 'in:low,medium,high,urgent'],
            'notes'        => ['nullable', 'string', 'max:1000'],
        ]);

        $task = $event->tasks()->create([
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'committee_id' => $validated['committee_id'] ?? null,
            'assigned_to'  => $validated['assigned_to'] ?? null,
            'due_date'     => $validated['due_date'] ?? null,
            'priority'     => $validated['priority'],
            'status'       => EventTask::STATUS_PENDING,
            'notes'        => $validated['notes'] ?? null,
            'created_by'   => Auth::id(),
        ]);

        // Notify assignee if assigned
        if ($task->assignee && $task->assigned_to !== Auth::id()) {
            $task->assignee->notify(new \App\Notifications\GeneralComsocNotification(
                title: "New Task Assigned: {$task->title}",
                message: "You have been assigned a task for '{$event->title}' with {$task->priority} priority.",
                actionUrl: route('tasks.my-tasks'),
                type: $task->priority === 'urgent' ? 'warning' : 'info'
            ));
        }

        return back()->with('success', "Task '{$task->title}' created successfully.");
    }

    /** Update task details */
    public function update(Request $request, EventTask $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'committee_id' => ['nullable', 'exists:committees,id'],
            'assigned_to'  => ['nullable', 'exists:users,id'],
            'due_date'     => ['nullable', 'date'],
            'priority'     => ['required', 'in:low,medium,high,urgent'],
            'status'       => ['required', 'in:pending,in_progress,completed,cancelled'],
            'notes'        => ['nullable', 'string', 'max:1000'],
        ]);

        $completedAt = $task->completed_at;
        if ($validated['status'] === EventTask::STATUS_COMPLETED && $task->status !== EventTask::STATUS_COMPLETED) {
            $completedAt = now();
        } elseif ($validated['status'] !== EventTask::STATUS_COMPLETED) {
            $completedAt = null;
        }

        $task->update([
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'committee_id' => $validated['committee_id'] ?? null,
            'assigned_to'  => $validated['assigned_to'] ?? null,
            'due_date'     => $validated['due_date'] ?? null,
            'priority'     => $validated['priority'],
            'status'       => $validated['status'],
            'completed_at' => $completedAt,
            'notes'        => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Task updated successfully.');
    }

    /** Rapid status update */
    public function updateStatus(Request $request, EventTask $task): JsonResponse|RedirectResponse
    {
        $this->authorize('updateStatus', $task);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        $completedAt = $validated['status'] === EventTask::STATUS_COMPLETED ? now() : null;

        $task->update([
            'status'       => $validated['status'],
            'completed_at' => $completedAt,
            'notes'        => $validated['notes'] ?? $task->notes,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $task->status,
                'label'   => $task->getStatusLabel(),
                'badge'   => $task->getStatusBadgeClasses(),
            ]);
        }

        return back()->with('success', "Task status updated to {$task->getStatusLabel()}.");
    }

    /** Delete task */
    public function destroy(EventTask $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $title = $task->title;
        $task->delete();

        return back()->with('success', "Task '{$title}' deleted successfully.");
    }
}
