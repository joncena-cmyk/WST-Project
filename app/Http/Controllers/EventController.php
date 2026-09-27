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
    /** Auto-transition past events to completed status */
    private function autoUpdateEventStatuses(): void
    {
        $today = now()->toDateString();
        Event::whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING])
            ->where('event_date', '<', $today)
            ->update(['status' => Event::STATUS_COMPLETED]);
    }

    /** Public event browse (published/ongoing upcoming events) */
    public function index(): View
    {
        $this->autoUpdateEventStatuses();

        $events = Event::with('creator')
            ->withCount(['activeRegistrations', 'attendance'])
            ->upcoming()
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->paginate(12);

        $upcomingCount = Event::upcoming()->count();
        $archivedCount = Event::archived()->count();

        return view('events.index', compact('events', 'upcomingCount', 'archivedCount'));
    }

    /** Public event archive (completed/archived finished events) */
    public function archive(Request $request): View
    {
        $this->autoUpdateEventStatuses();

        $query = Event::with(['creator', 'attendance', 'registrations', 'certificates'])
            ->withCount(['activeRegistrations', 'attendance', 'certificates'])
            ->archived()
            ->orderBy('event_date', 'desc');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($year = $request->get('year')) {
            $query->whereYear('event_date', $year);
        }

        $events = $query->paginate(9)->withQueryString();

        $totalArchived = Event::archived()->count();
        $totalAttendees = \App\Models\EventAttendance::whereHas('event', function ($q) {
            $q->archived();
        })->count();
        $totalCertificates = \App\Models\Certificate::whereHas('event', function ($q) {
            $q->archived();
        })->count();

        $years = Event::archived()
            ->orderBy('event_date', 'desc')
            ->pluck('event_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->year)
            ->unique()
            ->values();

        $upcomingCount = Event::upcoming()->count();
        $archivedCount = $totalArchived;

        return view('events.archive', compact(
            'events',
            'totalArchived',
            'totalAttendees',
            'totalCertificates',
            'years',
            'upcomingCount',
            'archivedCount'
        ));
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
        $event->load(['creator', 'requirements', 'approvals.reviewer', 'attendance.user', 'certificates']);

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

    /** Explicitly archive a completed event */
    public function archiveEvent(Event $event): RedirectResponse
    {
        $this->authorize('archive', $event);
        $event->update(['status' => Event::STATUS_ARCHIVED]);
        return redirect()->route('events.archive')
            ->with('success', "Event '{$event->title}' has been moved to the archive.");
    }
}
