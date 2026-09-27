<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\EventTask;
use App\Models\Membership;
use App\Models\PartnerOrganization;
use App\Models\Partnership;
use App\Models\Post;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Entry point: dispatches authenticated user to their role-specific dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard($user);
        }

        if ($user->isAdviser()) {
            return $this->adviserDashboard($user);
        }

        if ($user->isOfficerOrAbove()) {
            return $this->officerDashboard($user);
        }

        return $this->studentDashboard($user);
    }

    /**
     * Student / General Member Dashboard
     */
    private function studentDashboard(User $user): View
    {
        $activeMembership = $user->activeMembership();
        
        // Active registrations with eager loaded events
        $myRegistrations = EventRegistration::with('event')
            ->where('user_id', $user->id)
            ->where('status', EventRegistration::STATUS_REGISTERED)
            ->latest()
            ->take(4)
            ->get();

        // Upcoming published events
        $upcomingEvents = Event::whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING])
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->take(3)
            ->get();

        // My earned certificates
        $certificates = Certificate::with('event')
            ->where('user_id', $user->id)
            ->where('status', Certificate::STATUS_ISSUED)
            ->latest()
            ->take(3)
            ->get();

        // Attendance stats
        $attendedCount = EventAttendance::where('user_id', $user->id)->count();

        // Assigned tasks if in any event committees
        $myTasks = EventTask::with(['event', 'committee'])
            ->where('assigned_to', $user->id)
            ->where('status', '!=', EventTask::STATUS_COMPLETED)
            ->latest()
            ->take(3)
            ->get();

        return view('dashboards.student', compact(
            'user',
            'activeMembership',
            'myRegistrations',
            'upcomingEvents',
            'certificates',
            'attendedCount',
            'myTasks'
        ));
    }

    /**
     * Officer / President Dashboard
     */
    private function officerDashboard(User $user): View
    {
        // KPI Metrics
        $totalActiveMembers = Membership::where('status', Membership::STATUS_ACTIVE)->count();
        $activeEventsCount  = Event::whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING])->count();
        $totalTasksCount    = EventTask::count();
        $completedTasks     = EventTask::where('status', EventTask::STATUS_COMPLETED)->count();
        $taskVelocity       = $totalTasksCount > 0 ? round(($completedTasks / $totalTasksCount) * 100) : 0;

        // Pending Documents
        $pendingDocs = Document::with(['uploader', 'event'])
            ->whereIn('status', [Document::STATUS_SUBMITTED, Document::STATUS_UNDER_REVIEW])
            ->latest('submitted_at')
            ->take(3)
            ->get();

        // Active Events list
        $activeEvents = Event::whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING, Event::STATUS_DRAFT, Event::STATUS_APPROVED])
            ->orderBy('event_date', 'desc')
            ->take(4)
            ->get();

        // Tasks assigned to this officer
        $myTasks = EventTask::with(['event', 'committee'])
            ->where('assigned_to', $user->id)
            ->latest()
            ->take(4)
            ->get();

        // Recent Community posts
        $recentPosts = Post::with('user')->latest()->take(3)->get();

        // Role-specific stats & context
        $roleStats = [];

        if ($user->isVpExternal() || $user->isPresident()) {
            $roleStats['activePartnerships'] = Partnership::where('status', Partnership::STATUS_ACTIVE)->count();
            $roleStats['partnerOrgsCount'] = PartnerOrganization::where('status', PartnerOrganization::STATUS_ACTIVE)->count();
            $roleStats['pendingPartnerships'] = Partnership::where('status', Partnership::STATUS_UNDER_REVIEW)->count();
        }

        if ($user->isTreasurer() || $user->isPresident()) {
            $roleStats['pendingMemberships'] = Membership::where('status', Membership::STATUS_PENDING)->count();
            $roleStats['activeMemberships']  = Membership::where('status', Membership::STATUS_ACTIVE)->count();
        }

        if ($user->isAuditor() || $user->isPresident()) {
            $roleStats['totalAuditLogs'] = AuditLog::count();
            $roleStats['recentAuditLogs'] = AuditLog::with('user')->latest()->take(5)->get();
        }

        if ($user->isSecretary() || $user->isPresident()) {
            $roleStats['issuedCertificates'] = Certificate::where('status', Certificate::STATUS_ISSUED)->count();
            $roleStats['totalDocuments']     = Document::count();
        }

        if ($user->isPio() || $user->isPresident()) {
            $roleStats['officialPosts'] = Post::where('type', Post::TYPE_OFFICIAL)->count();
            $roleStats['totalPosts']    = Post::count();
        }

        if ($user->isProgramRep()) {
            $user->load('program');
            $roleStats['cohortMembers'] = $user->program_id
                ? Membership::where('program_id', $user->program_id)->where('status', Membership::STATUS_ACTIVE)->count()
                : 0;
            $roleStats['programName'] = $user->program?->short_name ?? 'Your Program';
        }

        return view('dashboards.officer', compact(
            'user',
            'totalActiveMembers',
            'activeEventsCount',
            'totalTasksCount',
            'completedTasks',
            'taskVelocity',
            'pendingDocs',
            'activeEvents',
            'myTasks',
            'recentPosts',
            'roleStats'
        ));
    }

    /**
     * Academic Adviser Dashboard
     */
    private function adviserDashboard(User $user): View
    {
        // Approval Queues
        $pendingEvents = Event::with('creator')
            ->whereIn('status', [Event::STATUS_SUBMITTED, Event::STATUS_UNDER_REVIEW])
            ->latest('created_at')
            ->get();

        $pendingDocuments = Document::with(['uploader', 'event'])
            ->whereIn('status', [Document::STATUS_SUBMITTED, Document::STATUS_UNDER_REVIEW])
            ->latest('submitted_at')
            ->get();

        // Overall stats
        $totalApprovedEvents = Event::whereIn('status', [Event::STATUS_APPROVED, Event::STATUS_PUBLISHED, Event::STATUS_ONGOING, Event::STATUS_COMPLETED])->count();
        $totalApprovedDocs   = Document::where('status', Document::STATUS_APPROVED)->count();
        $totalActiveMembers  = Membership::where('status', Membership::STATUS_ACTIVE)->count();

        // Upcoming Event schedule
        $upcomingSchedule = Event::whereIn('status', [Event::STATUS_APPROVED, Event::STATUS_PUBLISHED, Event::STATUS_ONGOING])
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->take(4)
            ->get();

        return view('dashboards.adviser', compact(
            'user',
            'pendingEvents',
            'pendingDocuments',
            'totalApprovedEvents',
            'totalApprovedDocs',
            'totalActiveMembers',
            'upcomingSchedule'
        ));
    }

    /**
     * Administrator Dashboard
     */
    private function adminDashboard(User $user): View
    {
        // System user breakdown
        $totalUsers        = User::count();
        $activeUsers       = User::where('account_status', User::STATUS_ACTIVE)->count();
        $suspendedUsers    = User::where('account_status', User::STATUS_SUSPENDED)->count();
        $officerCount      = User::whereIn('role', [User::ROLE_OFFICER, User::ROLE_PRESIDENT])->count();

        // Organization Metrics
        $totalEvents       = Event::count();
        $totalDocuments    = Document::count();
        $totalMemberships  = Membership::count();
        $totalPosts        = Post::count();

        // Programs breakdown
        $programs = Program::withCount('users')->get();

        // Recent user registrations
        $recentUsers = User::latest()->take(5)->get();

        return view('dashboards.admin', compact(
            'user',
            'totalUsers',
            'activeUsers',
            'suspendedUsers',
            'officerCount',
            'totalEvents',
            'totalDocuments',
            'totalMemberships',
            'totalPosts',
            'programs',
            'recentUsers'
        ));
    }
}
