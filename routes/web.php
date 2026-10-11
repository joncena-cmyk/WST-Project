<?php

use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\MembershipController as AdminMembershipController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AnalyticsReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CollaborationActivityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentReviewController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\EventApprovalController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\EventTaskController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PartnerOrganizationController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\PartnershipDocumentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VotingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/about', function () {
    return view('about');
})->name('about');

// Public Certificate Verification (no auth required)
Route::get('/verify-certificate/{certificate_number}', [CertificateController::class, 'verifyPublic'])
    ->name('certificates.verify');

// Public Partnership Response & Acceptance Portal (no auth required for partner orgs)
Route::get('/partnerships/{partnership}/respond/{token}', [PartnershipController::class, 'showRespond'])
    ->name('partnerships.respond');
Route::post('/partnerships/{partnership}/respond/{token}', [PartnershipController::class, 'submitRespond'])
    ->name('partnerships.respond.submit');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    // -----------------------------------------------------------------------
    // Profile
    // -----------------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // -----------------------------------------------------------------------
    // Notifications
    // -----------------------------------------------------------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                 [NotificationController::class, 'index'])->name('index');
        Route::get('/quick',            [NotificationController::class, 'quick'])->name('quick');
        Route::post('/{id}/read',       [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read',   [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}',          [NotificationController::class, 'destroy'])->name('destroy');
    });

    // -----------------------------------------------------------------------
    // My Membership (any authenticated user)
    // -----------------------------------------------------------------------
    Route::get('/membership',        [MembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/apply', [MembershipController::class, 'apply'])->name('membership.apply');

    // -----------------------------------------------------------------------
    // Community
    // -----------------------------------------------------------------------
    Route::prefix('community')->name('community.')->group(function () {
        Route::get('/',              [PostController::class, 'index'])->name('index');
        Route::post('/',             [PostController::class, 'store'])->name('store');
        Route::get('/{post}/edit',   [PostController::class, 'edit'])->name('edit');
        Route::patch('/{post}',      [PostController::class, 'update'])->name('update');
        Route::delete('/{post}',     [PostController::class, 'destroy'])->name('destroy');
        Route::post('/{post}/like',  [PostController::class, 'toggleLike'])->name('like');

        // Comments
        Route::post('/{post}/comments',     [CommentController::class, 'store'])->name('comments.store');
        Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}',[CommentController::class, 'destroy'])->name('comments.destroy');
    });

    // -----------------------------------------------------------------------
    // Events — public browse
    // -----------------------------------------------------------------------
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/',                                     [EventController::class, 'index'])->name('index');
        Route::get('/archive',                              [EventController::class, 'archive'])->name('archive');
        Route::get('/my-events',                            [EventRegistrationController::class, 'myEvents'])->name('my-events');
        Route::get('/manage',                               [EventController::class, 'manage'])->name('manage');
        Route::get('/create',                               [EventController::class, 'create'])->name('create');
        Route::post('/',                                    [EventController::class, 'store'])->name('store');
        Route::get('/{event}',                              [EventController::class, 'show'])->name('show');
        Route::get('/{event}/edit',                         [EventController::class, 'edit'])->name('edit');
        Route::patch('/{event}',                            [EventController::class, 'update'])->name('update');
        Route::post('/{event}/submit',                      [EventController::class, 'submit'])->name('submit');
        Route::post('/{event}/publish',                     [EventController::class, 'publish'])->name('publish');
        Route::post('/{event}/cancel',                      [EventController::class, 'cancel'])->name('cancel');
        Route::post('/{event}/archive',                     [EventController::class, 'archiveEvent'])->name('archive-event');
        Route::get('/{event}/participants',                 [EventController::class, 'participants'])->name('participants');
        Route::post('/{event}/register',                    [EventRegistrationController::class, 'store'])->name('register');
        Route::post('/registrations/{registration}/cancel', [EventRegistrationController::class, 'cancel'])->name('registration.cancel');
    });

    // -----------------------------------------------------------------------
    // Event Approval (adviser + admin)
    // -----------------------------------------------------------------------
    Route::prefix('events-approval')->name('events.approval.')->group(function () {
        Route::get('/',         [EventApprovalController::class, 'index'])->name('index');
        Route::get('/{event}',  [EventApprovalController::class, 'show'])->name('show');
        Route::post('/{event}', [EventApprovalController::class, 'review'])->name('review');
    });

    // -----------------------------------------------------------------------
    // QR Attendance (Phase 4)
    // -----------------------------------------------------------------------
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/pass/{event}',             [AttendanceController::class, 'showPass'])->name('pass');
        Route::get('/pass/{event}/download-qr', [AttendanceController::class, 'downloadQr'])->name('qr.download');
        Route::get('/scanner/{event}',          [AttendanceController::class, 'scannerPage'])->name('scanner');
        Route::post('/checkin/{event}',         [AttendanceController::class, 'checkIn'])->name('checkin');
        Route::post('/manual/{event}',          [AttendanceController::class, 'manualCheckIn'])->name('manual');
        Route::get('/list/{event}',             [AttendanceController::class, 'index'])->name('index');
    });

    // -----------------------------------------------------------------------
    // Certificates (Phase 5)
    // -----------------------------------------------------------------------
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/',                         [CertificateController::class, 'index'])->name('index');
        Route::get('/event/{event}/manage',     [CertificateController::class, 'manage'])->name('manage');
        Route::post('/event/{event}/issue',     [CertificateController::class, 'issue'])->name('issue');
        Route::post('/event/{event}/issue-all', [CertificateController::class, 'issueAll'])->name('issue-all');
        Route::post('/{certificate}/revoke',    [CertificateController::class, 'revoke'])->name('revoke');
        Route::get('/{certificate}',            [CertificateController::class, 'show'])->name('show');
    });

    // -----------------------------------------------------------------------
    // Committees & Tasks (Phase 6)
    // -----------------------------------------------------------------------
    // Personal Task Dashboard
    Route::get('/tasks',                        [EventTaskController::class, 'myTasks'])->name('tasks.my-tasks');
    Route::post('/tasks/{task}/status',         [EventTaskController::class, 'updateStatus'])->name('tasks.status');
    Route::patch('/tasks/{task}',               [EventTaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}',              [EventTaskController::class, 'destroy'])->name('tasks.destroy');

    // Event Committees & Tasks
    Route::prefix('events/{event}')->name('events.')->group(function () {
        Route::get('/committees',               [CommitteeController::class, 'index'])->name('committees.index');
        Route::post('/committees',              [CommitteeController::class, 'store'])->name('committees.store');
        Route::get('/tasks',                    [EventTaskController::class, 'eventTasks'])->name('tasks.index');
        Route::post('/tasks',                   [EventTaskController::class, 'store'])->name('tasks.store');
    });

    // Single Committee Workspace & Member Roster
    Route::prefix('committees')->name('committees.')->group(function () {
        Route::get('/{committee}',                      [CommitteeController::class, 'show'])->name('show');
        Route::patch('/{committee}',                    [CommitteeController::class, 'update'])->name('update');
        Route::delete('/{committee}',                   [CommitteeController::class, 'destroy'])->name('destroy');
        Route::post('/{committee}/members',             [CommitteeController::class, 'addMember'])->name('members.store');
        Route::delete('/{committee}/members/{user}',    [CommitteeController::class, 'removeMember'])->name('members.destroy');
    });

    // -----------------------------------------------------------------------
    // Documents & Approvals (Phase 7)
    // -----------------------------------------------------------------------
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/',                     [DocumentController::class, 'index'])->name('index');
        Route::get('/create',               [DocumentController::class, 'create'])->name('create');
        Route::post('/',                    [DocumentController::class, 'store'])->name('store');
        Route::get('/{document}',           [DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit',      [DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}',           [DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}',        [DocumentController::class, 'destroy'])->name('destroy');
        Route::post('/{document}/submit',   [DocumentController::class, 'submit'])->name('submit');
        Route::get('/{document}/download',  [DocumentController::class, 'download'])->name('download');
        Route::get('/{document}/preview',   [DocumentController::class, 'preview'])->name('preview');
    });

    // Adviser Document Review Queue
    Route::prefix('documents-review')->name('documents.review.')->group(function () {
        Route::get('/',                     [DocumentReviewController::class, 'index'])->name('index');
        Route::get('/{document}',           [DocumentReviewController::class, 'show'])->name('show');
        Route::post('/{document}',          [DocumentReviewController::class, 'review'])->name('process');
    });

    // -----------------------------------------------------------------------
    // Analytics & Reports (Phase 8)
    // -----------------------------------------------------------------------
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/',                                 [AnalyticsReportController::class, 'index'])->name('index');
        Route::get('/export/attendance/{event}',        [AnalyticsReportController::class, 'exportAttendance'])->name('export.attendance');
        Route::get('/export/memberships',               [AnalyticsReportController::class, 'exportMemberships'])->name('export.memberships');
        Route::get('/export/events',                    [AnalyticsReportController::class, 'exportEvents'])->name('export.events');
    });

    // -----------------------------------------------------------------------
    // Reports (User content reports)
    // -----------------------------------------------------------------------
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    // -----------------------------------------------------------------------
    // Moderation (officers+)
    // -----------------------------------------------------------------------
    Route::prefix('moderation')->name('moderation.')->group(function () {
        Route::get('/',                  [ModerationController::class, 'index'])->name('index');
        Route::post('/{report}/resolve', [ModerationController::class, 'resolve'])->name('resolve');
    });

    // -----------------------------------------------------------------------
    // Elections & Voting Hub (Phase 10)
    // -----------------------------------------------------------------------
    Route::prefix('elections')->name('elections.')->group(function () {
        Route::get('/',                                  [ElectionController::class, 'index'])->name('index');
        Route::get('/create',                            [ElectionController::class, 'create'])->name('create');
        Route::post('/',                                 [ElectionController::class, 'store'])->name('store');
        Route::get('/{election}',                        [ElectionController::class, 'show'])->name('show');
        Route::get('/{election}/edit',                   [ElectionController::class, 'edit'])->name('edit');
        Route::put('/{election}',                        [ElectionController::class, 'update'])->name('update');
        Route::delete('/{election}',                     [ElectionController::class, 'destroy'])->name('destroy');
        Route::post('/{election}/status',                [ElectionController::class, 'transitionStatus'])->name('status');

        // Candidacy filing
        Route::post('/{election}/apply',                 [CandidateController::class, 'store'])->name('apply');

        // Voting Booth, Receipts & Results
        Route::get('/{election}/vote',                   [VotingController::class, 'show'])->name('vote');
        Route::post('/{election}/vote',                  [VotingController::class, 'cast'])->middleware('throttle:3,1')->name('vote.cast');
        Route::get('/{election}/receipt',                [VotingController::class, 'receipt'])->name('receipt');
        Route::get('/{election}/results',                [VotingController::class, 'results'])->name('results');
        Route::post('/{election}/proclaim',              [VotingController::class, 'proclaim'])->name('proclaim');
    });

    // Candidates Review Queue (Officers & Advisers)
    Route::prefix('candidates')->name('candidates.')->group(function () {
        Route::get('/',                                  [CandidateController::class, 'index'])->name('index');
        Route::post('/{candidate}/review',              [CandidateController::class, 'review'])->name('review');
    });

    // -----------------------------------------------------------------------
    // Partnerships & External Collaborations (Phase 11)
    // -----------------------------------------------------------------------
    Route::resource('partner-organizations', PartnerOrganizationController::class);

    Route::prefix('partnerships')->name('partnerships.')->group(function () {
        Route::get('/',                                  [PartnershipController::class, 'index'])->name('index');
        Route::get('/create',                            [PartnershipController::class, 'create'])->name('create');
        Route::post('/',                                 [PartnershipController::class, 'store'])->name('store');
        Route::get('/{partnership}',                     [PartnershipController::class, 'show'])->name('show');
        Route::get('/{partnership}/edit',                [PartnershipController::class, 'edit'])->name('edit');
        Route::put('/{partnership}',                     [PartnershipController::class, 'update'])->name('update');
        Route::delete('/{partnership}',                  [PartnershipController::class, 'destroy'])->name('destroy');
        Route::post('/{partnership}/status',             [PartnershipController::class, 'transitionStatus'])->name('status');
        Route::post('/{partnership}/save-letter',        [PartnershipController::class, 'saveLetter'])->name('save-letter');
        Route::post('/{partnership}/send-email',          [PartnershipController::class, 'sendEmail'])->name('send-email');
        Route::post('/{partnership}/submit-to-adviser',  [PartnershipController::class, 'submitToAdviser'])->name('submit-to-adviser');
        Route::post('/{partnership}/endorse',            [PartnershipController::class, 'endorse'])->name('endorse');
        Route::post('/{partnership}/spawn-event',        [PartnershipController::class, 'spawnEvent'])->name('spawn-event');

        // Activities
        Route::post('/{partnership}/activities',         [CollaborationActivityController::class, 'store'])->name('activities.store');
        Route::patch('/activities/{activity}',           [CollaborationActivityController::class, 'updateStatus'])->name('activities.update-status');
        Route::delete('/activities/{activity}',          [CollaborationActivityController::class, 'destroy'])->name('activities.destroy');

        // Documents
        Route::post('/{partnership}/documents',          [PartnershipDocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}/download',     [PartnershipDocumentController::class, 'download'])->name('documents.download');
        Route::delete('/documents/{document}',           [PartnershipDocumentController::class, 'destroy'])->name('documents.destroy');
    });

    // -----------------------------------------------------------------------
    // Admin & Officer Console Routes
    // -----------------------------------------------------------------------
    Route::prefix('admin')->name('admin.')->group(function () {
        // User Management (admin only)
        Route::middleware('admin.only')->group(function () {
            Route::get('users',           [AdminUserController::class, 'index'])->name('users.index');
            Route::get('users/{user}',    [AdminUserController::class, 'show'])->name('users.show');
            Route::patch('users/{user}',  [AdminUserController::class, 'update'])->name('users.update');
        });

        // Membership Management (officers+) — inside admin prefix but accessible to officers
        Route::get('memberships',                       [AdminMembershipController::class, 'index'])->name('memberships.index');
        Route::get('memberships/create',                [AdminMembershipController::class, 'create'])->name('memberships.create');
        Route::post('memberships',                      [AdminMembershipController::class, 'store'])->name('memberships.store');
        Route::get('memberships/{membership}/edit',     [AdminMembershipController::class, 'edit'])->name('memberships.edit');
        Route::patch('memberships/{membership}',        [AdminMembershipController::class, 'update'])->name('memberships.update');
        Route::post('memberships/{membership}/approve', [AdminMembershipController::class, 'approve'])->name('memberships.approve');
        Route::post('memberships/{membership}/reject',  [AdminMembershipController::class, 'reject'])->name('memberships.reject');

        // Security & Audit Logs (Phase 9 - accessible to admin, auditor, president, adviser)
        Route::get('audit-logs',                    [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
    });
});

require __DIR__.'/auth.php';
