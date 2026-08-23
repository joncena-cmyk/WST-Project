<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Comment;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\EventTask;
use App\Models\Membership;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsReportController extends Controller
{
    private function authorizeAnalytics(): void
    {
        abort_unless(
            Auth::check() && (Auth::user()->isOfficerOrAbove() || Auth::user()->isAdviser() || Auth::user()->isAdmin()),
            403,
            'Only officers, advisers, and administrators can access organization reports & analytics.'
        );
    }

    /**
     * Analytics & Reports Main Dashboard
     */
    public function index(Request $request): View
    {
        $this->authorizeAnalytics();

        // 1. Overall Event Participation KPIs
        $totalEventsCount        = Event::whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING, Event::STATUS_COMPLETED])->count();
        $totalRegistrationsCount = EventRegistration::where('status', '!=', EventRegistration::STATUS_CANCELLED)->count();
        $totalAttendanceCount    = EventAttendance::count();
        $overallTurnoutRate      = $totalRegistrationsCount > 0
            ? round(($totalAttendanceCount / $totalRegistrationsCount) * 100, 1)
            : 0;

        // 2. Event Performance Breakdown
        $events = Event::withCount([
            'registrations as valid_registrations_count' => fn($q) => $q->where('status', '!=', EventRegistration::STATUS_CANCELLED),
            'attendances',
            'certificates',
        ])
        ->orderBy('event_date', 'desc')
        ->paginate(10);

        // 3. Membership Distribution by Program
        $programs = Program::withCount([
            'users',
            'users as active_members_count' => fn($q) => $q->whereHas('memberships', fn($m) => $m->where('status', Membership::STATUS_ACTIVE)),
        ])->get();

        $totalActiveMembers  = Membership::where('status', Membership::STATUS_ACTIVE)->count();
        $totalPendingMembers = Membership::where('status', Membership::STATUS_PENDING)->count();

        // 4. Task Velocity & Committees
        $totalTasks     = EventTask::count();
        $completedTasks = EventTask::where('status', EventTask::STATUS_COMPLETED)->count();
        $inProgressTasks= EventTask::where('status', EventTask::STATUS_IN_PROGRESS)->count();
        $pendingTasks   = EventTask::where('status', EventTask::STATUS_PENDING)->count();
        $taskVelocity   = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // 5. Certificates Issued
        $totalCertificatesIssued = Certificate::where('status', Certificate::STATUS_ISSUED)->count();

        // 6. Community Engagement
        $totalPosts     = Post::count();
        $totalComments  = Comment::count();
        $totalReactions = PostReaction::count();

        return view('reports.analytics', compact(
            'totalEventsCount',
            'totalRegistrationsCount',
            'totalAttendanceCount',
            'overallTurnoutRate',
            'events',
            'programs',
            'totalActiveMembers',
            'totalPendingMembers',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'pendingTasks',
            'taskVelocity',
            'totalCertificatesIssued',
            'totalPosts',
            'totalComments',
            'totalReactions'
        ));
    }

    /**
     * Export Event Attendance Roster as CSV
     */
    public function exportAttendance(Event $event): StreamedResponse
    {
        $this->authorizeAnalytics();

        $attendances = EventAttendance::with(['user.program'])
            ->where('event_id', $event->id)
            ->orderBy('checked_in_at')
            ->get();

        $filename = 'attendance_' . \Illuminate\Support\Str::slug($event->title) . '_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($attendances, $event) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header info
            fputcsv($handle, ['COMSOC Hub — Event Attendance Roster']);
            fputcsv($handle, ['Event:', $event->title]);
            fputcsv($handle, ['Date:', $event->event_date->format('M d, Y') . ' (' . $event->start_time . ' - ' . $event->end_time . ')']);
            fputcsv($handle, ['Venue:', $event->venue]);
            fputcsv($handle, ['Total Checked-In:', $attendances->count()]);
            fputcsv($handle, []); // Blank line

            // Column Titles
            fputcsv($handle, [
                '#',
                'Student Name',
                'Student ID',
                'Program',
                'Email',
                'Check-In Time',
                'Status',
            ]);

            foreach ($attendances as $idx => $att) {
                fputcsv($handle, [
                    $idx + 1,
                    $att->user->name,
                    $att->user->student_id ?? 'N/A',
                    $att->user->program?->code ?? 'N/A',
                    $att->user->email,
                    $att->checked_in_at->format('Y-m-d g:i A'),
                    ucfirst($att->attendance_status),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export Membership Master Roster as CSV
     */
    public function exportMemberships(): StreamedResponse
    {
        $this->authorizeAnalytics();

        $memberships = Membership::with(['user.program'])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'comsoc_membership_roster_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($memberships) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['COMSOC Hub — Official Membership Master Roster']);
            fputcsv($handle, ['Generated At:', date('Y-m-d H:i:s')]);
            fputcsv($handle, ['Total Members:', $memberships->count()]);
            fputcsv($handle, []);

            fputcsv($handle, [
                '#',
                'Full Name',
                'Student ID',
                'Program',
                'Year Level',
                'Email',
                'Membership Status',
                'Start Date',
                'End Date',
                'Application Date',
            ]);

            foreach ($memberships as $idx => $m) {
                fputcsv($handle, [
                    $idx + 1,
                    $m->user->name,
                    $m->user->student_id ?? 'N/A',
                    $m->user->program?->code ?? 'N/A',
                    $m->user->year_level ? 'Year ' . $m->user->year_level : 'N/A',
                    $m->user->email,
                    ucfirst($m->status),
                    $m->start_date ? $m->start_date->format('Y-m-d') : 'N/A',
                    $m->end_date ? $m->end_date->format('Y-m-d') : 'N/A',
                    $m->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export All Events Summary as CSV
     */
    public function exportEvents(): StreamedResponse
    {
        $this->authorizeAnalytics();

        $events = Event::withCount([
            'registrations as valid_registrations_count' => fn($q) => $q->where('status', '!=', EventRegistration::STATUS_CANCELLED),
            'attendances',
            'certificates',
        ])
        ->orderBy('event_date', 'desc')
        ->get();

        $filename = 'comsoc_events_summary_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($events) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['COMSOC Hub — Events & Activity Summary']);
            fputcsv($handle, ['Generated At:', date('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                '#',
                'Event Title',
                'Event Date',
                'Venue',
                'Status',
                'Registered Count',
                'Attended Count',
                'Turnout Rate (%)',
                'Certificates Issued',
            ]);

            foreach ($events as $idx => $event) {
                $rate = $event->valid_registrations_count > 0
                    ? round(($event->attendances_count / $event->valid_registrations_count) * 100, 1) . '%'
                    : '0.0%';

                fputcsv($handle, [
                    $idx + 1,
                    $event->title,
                    $event->event_date->format('Y-m-d'),
                    $event->venue,
                    $event->getStatusLabel(),
                    $event->valid_registrations_count,
                    $event->attendances_count,
                    $rate,
                    $event->certificates_count,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
