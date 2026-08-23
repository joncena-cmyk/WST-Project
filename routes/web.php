<?php

use App\Http\Controllers\Admin\MembershipController as AdminMembershipController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentReviewController;
use App\Http\Controllers\EventApprovalController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\EventTaskController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    // -----------------------------------------------------------------------
    // Profile
    // -----------------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // -----------------------------------------------------------------------
    // My Membership (any authenticated user)
    // -----------------------------------------------------------------------
    Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');

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
        Route::get('/pass/{event}',    [AttendanceController::class, 'showPass'])->name('pass');
        Route::get('/scanner/{event}', [AttendanceController::class, 'scannerPage'])->name('scanner');
        Route::post('/checkin/{event}',[AttendanceController::class, 'checkIn'])->name('checkin');
        Route::post('/manual/{event}', [AttendanceController::class, 'manualCheckIn'])->name('manual');
        Route::get('/list/{event}',    [AttendanceController::class, 'index'])->name('index');
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
    // Reports
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
    // Admin — User Management (admin only)
    // -----------------------------------------------------------------------
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('users',           [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}',    [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('users/{user}',  [AdminUserController::class, 'update'])->name('users.update');

        // Membership Management (officers+)
        Route::get('memberships',                   [AdminMembershipController::class, 'index'])->name('memberships.index');
        Route::get('memberships/create',            [AdminMembershipController::class, 'create'])->name('memberships.create');
        Route::post('memberships',                  [AdminMembershipController::class, 'store'])->name('memberships.store');
        Route::get('memberships/{membership}/edit', [AdminMembershipController::class, 'edit'])->name('memberships.edit');
        Route::patch('memberships/{membership}',    [AdminMembershipController::class, 'update'])->name('memberships.update');
    });
});

require __DIR__.'/auth.php';
