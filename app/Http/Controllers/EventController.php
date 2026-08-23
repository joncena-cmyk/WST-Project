<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRequirement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    /** Public event browse (published/ongoing) */
    public function index(): View
    {
        $events = Event::with('creator')
            ->whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING])
            ->orderBy('event_date')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    /** Officer: list their own events + all for admin */
    public function manage(): View
    {
        $this->authorize('create', Event::class);

        $query = Event::with('creator')->latest();

        if (!Auth::user()->isAdmin()) {
            $query->where('created_by', Auth::id());
        }

        $events = $query->paginate(15);

        return view('events.manage', compact('events'));
    }

    /** Show single event detail */
    public function show(Event $event): View
    {
        $this->authorize('view', $event);
        $event->load(['creator', 'requirements', 'approvals.reviewer']);

        $userRegistration = $event->registrations()
            ->where('user_id', Auth::id())
            ->first();

        return view('events.show', compact('event', 'userRegistration'));
    }

    /** Create form */
    public function create(): View
    {
        $this->authorize('create', Event::class);
        return view('events.create');
    }

    /** Store new event */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $data = $request->validate([
            'title'                 => ['required', 'string', 'max:255'],
            'description'           => ['required', 'string', 'max:10000'],
            'event_date'            => ['required', 'date', 'after_or_equal:today'],
            'start_time'            => ['required', 'date_format:H:i'],
            'end_time'              => ['nullable', 'date_format:H:i', 'after:start_time'],
            'venue'                 => ['required', 'string', 'max:255'],
            'registration_deadline' => ['nullable', 'date', 'before_or_equal:event_date'],
            'participant_limit'     => ['nullable', 'integer', 'min:1'],
            'image'                 => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'requirements'          => ['nullable', 'array'],
            'requirements.*'        => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $data['created_by'] = Auth::id();
        $data['status']     = Event::STATUS_DRAFT;

        $event = Event::create($data);

        // Store requirements
        if (!empty($data['requirements'])) {
            foreach (array_filter($data['requirements']) as $req) {
                EventRequirement::create(['event_id' => $event->id, 'description' => $req]);
            }
        }

        return redirect()->route('events.manage')
            ->with('success', 'Event created as draft.');
    }

    /** Edit form */
    public function edit(Event $event): View
    {
        $this->authorize('update', $event);
        $event->load('requirements');
        return view('events.edit', compact('event'));
    }

    /** Update event */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $data = $request->validate([
            'title'                 => ['required', 'string', 'max:255'],
            'description'           => ['required', 'string', 'max:10000'],
            'event_date'            => ['required', 'date'],
            'start_time'            => ['required', 'date_format:H:i'],
            'end_time'              => ['nullable', 'date_format:H:i'],
            'venue'                 => ['required', 'string', 'max:255'],
            'registration_deadline' => ['nullable', 'date'],
            'participant_limit'     => ['nullable', 'integer', 'min:1'],
            'image'                 => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'requirements'          => ['nullable', 'array'],
            'requirements.*'        => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('image')) {
            if ($event->image) Storage::disk('public')->delete($event->image);
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        // Rebuild requirements
        $event->requirements()->delete();
        if (!empty($data['requirements'])) {
            foreach (array_filter($data['requirements']) as $req) {
                EventRequirement::create(['event_id' => $event->id, 'description' => $req]);
            }
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated.');
    }

    /** Submit for adviser review */
    public function submit(Event $event): RedirectResponse
    {
        $this->authorize('submit', $event);
        $event->update(['status' => Event::STATUS_SUBMITTED]);
        return back()->with('success', 'Event submitted for review.');
    }

    /** Publish after approval */
    public function publish(Event $event): RedirectResponse
    {
        $this->authorize('publish', $event);
        $event->update([
            'status'       => Event::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        return back()->with('success', 'Event is now published and visible to students.');
    }

    /** Cancel event */
    public function cancel(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('cancel', $event);
        $data = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);
        $event->update([
            'status'              => Event::STATUS_CANCELLED,
            'cancelled_at'        => now(),
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
        ]);
        return redirect()->route('events.manage')
            ->with('success', 'Event cancelled.');
    }

    /** View participants list */
    public function participants(Event $event): View
    {
        $this->authorize('viewParticipants', $event);
        $registrations = $event->registrations()
            ->with('user.program')
            ->orderBy('created_at')
            ->get();
        return view('events.participants', compact('event', 'registrations'));
    }
}
