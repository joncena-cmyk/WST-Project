<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CertificateController extends Controller
{
    /**
     * Student: list their own certificates.
     */
    public function index(): View
    {
        $certificates = Certificate::with(['event', 'issuedBy'])
            ->where('user_id', Auth::id())
            ->where('status', Certificate::STATUS_ISSUED)
            ->latest('issued_at')
            ->get();

        return view('certificates.index', compact('certificates'));
    }

    /**
     * Student: view/print a single certificate.
     */
    public function show(Certificate $certificate): View
    {
        // Only the owner can view their certificate
        abort_unless($certificate->user_id === Auth::id() || Auth::user()->isOfficerOrAbove(), 403);
        abort_unless($certificate->status === Certificate::STATUS_ISSUED, 404);

        $certificate->load(['user.program', 'event', 'issuedBy']);

        return view('certificates.show', compact('certificate'));
    }

    /**
     * Officer: manage certificates for an event — list eligible attendees.
     */
    public function manage(Event $event): View
    {
        abort_unless(Auth::user()->isOfficerOrAbove(), 403);

        // Get all attendees for this event
        $attendance = EventAttendance::with(['user.program'])
            ->where('event_id', $event->id)
            ->orderBy('checked_in_at')
            ->get();

        // Get already issued certificates for this event
        $issuedCertificates = Certificate::where('event_id', $event->id)
            ->pluck('user_id')
            ->toArray();

        return view('certificates.manage', compact('event', 'attendance', 'issuedCertificates'));
    }

    /**
     * Officer: issue certificate to a single attendee.
     */
    public function issue(Request $request, Event $event): RedirectResponse
    {
        abort_unless(Auth::user()->isOfficerOrAbove(), 403);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        // Check the user actually attended
        $attendance = EventAttendance::where('event_id', $event->id)
            ->where('user_id', $data['user_id'])
            ->first();

        if (!$attendance) {
            return back()->with('error', 'This user did not attend the event and cannot receive a certificate.');
        }

        // Prevent duplicate
        $existing = Certificate::where('event_id', $event->id)
            ->where('user_id', $data['user_id'])
            ->first();

        if ($existing) {
            return back()->with('error', 'A certificate has already been issued to this attendee.');
        }

        Certificate::create([
            'user_id'            => $data['user_id'],
            'event_id'           => $event->id,
            'attendance_id'      => $attendance->id,
            'certificate_number' => Certificate::generateNumber(),
            'status'             => Certificate::STATUS_ISSUED,
            'issued_by'          => Auth::id(),
            'issued_at'          => now(),
        ]);

        return back()->with('success', 'Certificate issued successfully.');
    }

    /**
     * Officer: issue certificates to ALL attendees of an event at once.
     */
    public function issueAll(Event $event): RedirectResponse
    {
        abort_unless(Auth::user()->isOfficerOrAbove(), 403);

        $attendance = EventAttendance::where('event_id', $event->id)->get();

        if ($attendance->isEmpty()) {
            return back()->with('error', 'No attendees found for this event.');
        }

        $issued = 0;
        foreach ($attendance as $record) {
            // Skip if already issued
            $exists = Certificate::where('event_id', $event->id)
                ->where('user_id', $record->user_id)
                ->exists();

            if ($exists) continue;

            Certificate::create([
                'user_id'            => $record->user_id,
                'event_id'           => $event->id,
                'attendance_id'      => $record->id,
                'certificate_number' => Certificate::generateNumber(),
                'status'             => Certificate::STATUS_ISSUED,
                'issued_by'          => Auth::id(),
                'issued_at'          => now(),
            ]);
            $issued++;
        }

        return back()->with('success', "Issued {$issued} certificate(s) to all eligible attendees.");
    }

    /**
     * Officer: revoke a certificate.
     */
    public function revoke(Request $request, Certificate $certificate): RedirectResponse
    {
        abort_unless(Auth::user()->isOfficerOrAbove(), 403);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $certificate->update([
            'status'             => Certificate::STATUS_REVOKED,
            'revoked_at'         => now(),
            'revocation_reason'  => $data['reason'] ?? null,
        ]);

        return back()->with('success', 'Certificate revoked.');
    }
}
