<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Student: show their QR pass for a registered event.
     */
    public function showPass(Event $event): View
    {
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', EventRegistration::STATUS_REGISTERED)
            ->firstOrFail();

        $alreadyCheckedIn = EventAttendance::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->exists();

        return view('attendance.pass', compact('event', 'registration', 'alreadyCheckedIn'));
    }

    /**
     * Officer: QR scanner page for an event.
     */
    public function scannerPage(Event $event): View|RedirectResponse
    {
        abort_unless(Auth::user()->isOfficerOrAbove(), 403);

        if (!in_array($event->status, [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING, Event::STATUS_COMPLETED])) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Attendance scanning is only available once the event is published or ongoing.');
        }

        $attendanceCount = $event->attendance()->count();
        $registeredCount = $event->activeRegistrations()->count();

        return view('attendance.scanner', compact('event', 'attendanceCount', 'registeredCount'));
    }

    /**
     * Officer: process QR scan — validate and record check-in (AJAX).
     */
    public function checkIn(Request $request, Event $event): JsonResponse
    {
        abort_unless(Auth::user()->isOfficerOrAbove(), 403);

        $data = $request->validate([
            'qr_code' => ['required', 'string'],
        ]);

        // Find the registration by QR code
        $registration = EventRegistration::with(['user.program'])
            ->where('qr_code', $data['qr_code'])
            ->where('event_id', $event->id)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. No matching registration found for this event.',
                'type'    => 'invalid',
            ], 422);
        }

        if ($registration->status === EventRegistration::STATUS_CANCELLED) {
            return response()->json([
                'success' => false,
                'message' => 'Registration has been cancelled. This student cannot check in.',
                'type'    => 'cancelled',
            ], 422);
        }

        // Check for duplicate attendance
        $existing = EventAttendance::where('event_id', $event->id)
            ->where('user_id', $registration->user_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in at ' . $existing->checked_in_at->format('g:i A'),
                'type'    => 'duplicate',
                'student' => [
                    'name'         => $registration->user->name,
                    'student_id'   => $registration->user->student_id ?? '—',
                    'program'      => $registration->user->program?->code ?? '—',
                    'checked_in_at'=> $existing->checked_in_at->format('M d, Y g:i A'),
                ],
            ], 409);
        }

        // Determine if late (after event start time)
        $eventStart   = \Carbon\Carbon::parse($event->event_date->format('Y-m-d') . ' ' . $event->start_time);
        $attendStatus = now()->isAfter($eventStart->addMinutes(15))
            ? EventAttendance::STATUS_LATE
            : EventAttendance::STATUS_PRESENT;

        // Record attendance
        $attendance = EventAttendance::create([
            'event_id'          => $event->id,
            'user_id'           => $registration->user_id,
            'registration_id'   => $registration->id,
            'recorded_by'       => Auth::id(),
            'checked_in_at'     => now(),
            'attendance_status' => $attendStatus,
        ]);

        // Update registration status to attended
        $registration->update(['status' => EventRegistration::STATUS_ATTENDED]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'type'    => 'success',
            'student' => [
                'name'             => $registration->user->name,
                'student_id'       => $registration->user->student_id ?? '—',
                'program'          => $registration->user->program?->code ?? '—',
                'avatar'           => $registration->user->getProfilePictureUrl(),
                'checked_in_at'    => $attendance->checked_in_at->format('g:i A'),
                'attendance_status'=> ucfirst($attendStatus),
            ],
        ]);
    }

    /**
     * Officer: manual token check-in (fallback when camera is unavailable).
     */
    public function manualCheckIn(Request $request, Event $event): RedirectResponse
    {
        abort_unless(Auth::user()->isOfficerOrAbove(), 403);

        $data = $request->validate([
            'qr_code' => ['required', 'string', 'size:32'],
        ]);

        $registration = EventRegistration::where('qr_code', $data['qr_code'])
            ->where('event_id', $event->id)
            ->where('status', EventRegistration::STATUS_REGISTERED)
            ->first();

        if (!$registration) {
            return back()->with('error', 'Invalid QR token. No active registration found.');
        }

        $duplicate = EventAttendance::where('event_id', $event->id)
            ->where('user_id', $registration->user_id)
            ->exists();

        if ($duplicate) {
            return back()->with('error', $registration->user->name . ' has already checked in.');
        }

        EventAttendance::create([
            'event_id'          => $event->id,
            'user_id'           => $registration->user_id,
            'registration_id'   => $registration->id,
            'recorded_by'       => Auth::id(),
            'checked_in_at'     => now(),
            'attendance_status' => EventAttendance::STATUS_PRESENT,
        ]);

        $registration->update(['status' => EventRegistration::STATUS_ATTENDED]);

        return back()->with('success', $registration->user->name . ' checked in successfully.');
    }

    /**
     * Officer: attendance list for an event.
     */
    public function index(Event $event): View
    {
        abort_unless(Auth::user()->isOfficerOrAbove(), 403);

        $attendance = EventAttendance::with(['user.program', 'recordedBy'])
            ->where('event_id', $event->id)
            ->orderBy('checked_in_at')
            ->get();

        $registeredCount = $event->registrations()->where('status', '!=', 'cancelled')->count();
        $attendedCount   = $attendance->count();
        $absentCount     = $registeredCount - $attendedCount;

        return view('attendance.index', compact('event', 'attendance', 'registeredCount', 'attendedCount', 'absentCount'));
    }
}
