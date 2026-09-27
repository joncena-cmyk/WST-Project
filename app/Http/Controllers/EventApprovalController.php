<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventApproval;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventApprovalController extends Controller
{
    private function authorizeAdviser(): void
    {
        abort_unless(
            Auth::check() && (Auth::user()->isAdviser() || Auth::user()->isAdmin()),
            403,
            'Only the Adviser can review events.'
        );
    }

    /** Adviser queue — submitted and under-review events */
    public function index(): View
    {
        $this->authorizeAdviser();

        $pending = Event::with('creator')
            ->whereIn('status', [Event::STATUS_SUBMITTED, Event::STATUS_UNDER_REVIEW])
            ->latest()
            ->get();

        $history = EventApproval::with(['event', 'reviewer'])
            ->latest()
            ->paginate(10);

        return view('events.approval.index', compact('pending', 'history'));
    }

    /** Show single event for review */
    public function show(Event $event): View
    {
        $this->authorizeAdviser();

        // Mark as under review when adviser opens it
        if ($event->status === Event::STATUS_SUBMITTED) {
            $event->update(['status' => Event::STATUS_UNDER_REVIEW]);
        }

        $event->load(['creator', 'requirements', 'approvals.reviewer']);
        return view('events.approval.show', compact('event'));
    }

    /** Process review decision */
    public function review(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeAdviser();

        // Gracefully handle if event is already reviewed
        if (!in_array($event->status, [Event::STATUS_SUBMITTED, Event::STATUS_UNDER_REVIEW])) {
            return redirect()->route('events.approval.index')
                ->with('info', "Event '{$event->title}' has already been reviewed ({$event->getStatusLabel()}).");
        }

        $data = $request->validate([
            'action'  => ['required', 'in:approved,rejected,returned'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $statusMap = [
            'approved' => Event::STATUS_APPROVED,
            'rejected' => Event::STATUS_CANCELLED,
            'returned' => Event::STATUS_RETURNED,
        ];

        $messages = [
            'approved' => 'Event has been approved and is now authorized for publishing.',
            'rejected' => 'Event proposal has been rejected.',
            'returned' => 'Event has been returned for revisions.',
        ];

        $event->update(['status' => $statusMap[$data['action']]]);

        EventApproval::create([
            'event_id'    => $event->id,
            'reviewed_by' => Auth::id(),
            'action'      => $data['action'],
            'remarks'     => $data['remarks'] ?? null,
        ]);

        // Notify event creator
        if ($event->creator) {
            $type = $data['action'] === 'approved' ? 'success' : ($data['action'] === 'returned' ? 'warning' : 'danger');
            $event->creator->notify(new \App\Notifications\GeneralComsocNotification(
                title: "Event " . ucfirst($data['action']) . ": " . $event->title,
                message: "Adviser decision: " . ($messages[$data['action']] ?? '') . ($data['remarks'] ? " Remarks: " . $data['remarks'] : ""),
                actionUrl: route('events.show', $event),
                type: $type
            ));
        }

        return redirect()->route('events.approval.index')
            ->with('success', $messages[$data['action']]);
    }
}
