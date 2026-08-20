<?php

use App\Http\Controllers\Admin\MembershipController as AdminMembershipController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EventApprovalController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
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
