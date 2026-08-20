<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventRegistrationController extends Controller
{
    /** Register for an event */
    public function store(Event $event): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isActive()) {
            return back()->with('error', 'Your account is not active.');
        }

        if (!$event->isRegistrationOpen()) {
            return back()->with('error', 'Registration is not open for this event.');
        }

        if ($event->isRegisteredBy($user)) {
            return back()->with('info', 'You are already registered for this event.');
        }

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id'  => $user->id,
            'status'   => EventRegistration::STATUS_REGISTERED,
            'qr_code'  => EventRegistration::generateQrCode(),
        ]);

        return back()->with('success', 'You have successfully registered for this event!');
    }

    /** Cancel own registration */
    public function cancel(Request $request, EventRegistration $registration): RedirectResponse
    {
        if ($registration->user_id !== Auth::id()) {
            abort(403);
        }

        if ($registration->status !== EventRegistration::STATUS_REGISTERED) {
            return back()->with('error', 'This registration cannot be cancelled.');
        }

        $data = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $registration->update([
            'status'              => EventRegistration::STATUS_CANCELLED,
            'cancelled_at'        => now(),
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
        ]);

        return back()->with('success', 'Your registration has been cancelled.');
    }

    /** My registered events */
    public function myEvents(): View
    {
        $registrations = EventRegistration::with('event')
            ->where('user_id', Auth::id())
            ->where('status', EventRegistration::STATUS_REGISTERED)
            ->latest()
            ->get();

        return view('events.my-events', compact('registrations'));
    }
}
