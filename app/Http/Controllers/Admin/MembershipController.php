<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MembershipController extends Controller
{
    private function authorizeOfficer(): void
    {
        abort_unless(Auth::check() && Auth::user()->isOfficerOrAbove(), 403);
    }

    /** List all memberships */
    public function index(Request $request): View
    {
        $this->authorizeOfficer();

        $query = Membership::with(['user.program', 'program', 'approvedBy'])->latest();

        if ($search = $request->get('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($year = $request->get('academic_year')) {
            $query->where('academic_year', $year);
        }

        $memberships   = $query->paginate(20)->withQueryString();
        $academicYears = Membership::distinct()->pluck('academic_year')->sortDesc()->values();

        return view('admin.memberships.index', compact('memberships', 'academicYears'));
    }

    /** Show create form */
    public function create(): View
    {
        $this->authorizeOfficer();
        $users    = User::orderBy('name')->get(['id', 'name', 'email', 'student_id']);
        $programs = Program::where('is_active', true)->orderBy('short_name')->get();
        return view('admin.memberships.create', compact('users', 'programs'));
    }

    /** Store a new membership */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeOfficer();

        $data = $request->validate([
            'user_id'       => ['required', 'exists:users,id'],
            'program_id'    => ['nullable', 'exists:programs,id'],
            'academic_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'status'        => ['required', Rule::in([
                Membership::STATUS_PENDING,
                Membership::STATUS_ACTIVE,
                Membership::STATUS_EXPIRED,
                Membership::STATUS_SUSPENDED,
            ])],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        $exists = Membership::where('user_id', $data['user_id'])
            ->where('academic_year', $data['academic_year'])
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->withErrors(['academic_year' => 'This user already has a membership for that academic year.']);
        }

        if ($data['status'] === Membership::STATUS_ACTIVE) {
            $data['approved_by'] = Auth::id();
            $data['approved_at'] = now();
        }

        Membership::create($data);

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership created successfully.');
    }

    /** Show edit form */
    public function edit(Membership $membership): View
    {
        $this->authorizeOfficer();
        $membership->load(['user', 'program']);
        $programs = Program::where('is_active', true)->orderBy('short_name')->get();
        return view('admin.memberships.edit', compact('membership', 'programs'));
    }

    /** Update a membership */
    public function update(Request $request, Membership $membership): RedirectResponse
    {
        $this->authorizeOfficer();

        $data = $request->validate([
            'program_id' => ['nullable', 'exists:programs,id'],
            'status'     => ['required', Rule::in([
                Membership::STATUS_PENDING,
                Membership::STATUS_ACTIVE,
                Membership::STATUS_EXPIRED,
                Membership::STATUS_SUSPENDED,
            ])],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['status'] === Membership::STATUS_ACTIVE && $membership->status !== Membership::STATUS_ACTIVE) {
            $data['approved_by'] = Auth::id();
            $data['approved_at'] = now();
        }

        $membership->update($data);

        return back()->with('success', 'Membership updated successfully.');
    }

    /** Approve and activate a membership application */
    public function approve(Request $request, Membership $membership): RedirectResponse
    {
        $this->authorizeOfficer();

        $membership->update([
            'status'      => Membership::STATUS_ACTIVE,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'start_date'  => $membership->start_date ?? now(),
            'end_date'    => $membership->end_date ?? now()->addYear(),
        ]);

        // Promote student to member if applicable
        if ($membership->user && $membership->user->isStudent()) {
            $membership->user->update(['role' => User::ROLE_MEMBER]);
        }

        // Notify user
        if ($membership->user) {
            $membership->user->notify(new \App\Notifications\GeneralComsocNotification(
                title: "🎉 COMSOC Membership Approved!",
                message: "Your membership for Academic Year {$membership->academic_year} has been verified and activated. Welcome to the official CCIS Computer Society!",
                actionUrl: route('membership.index'),
                type: 'success'
            ));
        }

        return back()->with('success', "Membership for {$membership->user->name} has been approved and activated.");
    }

    /** Reject a membership application */
    public function reject(Request $request, Membership $membership): RedirectResponse
    {
        $this->authorizeOfficer();

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $membership->update([
            'status'           => Membership::STATUS_REJECTED,
            'rejection_reason' => $data['rejection_reason'],
        ]);

        // Notify user
        if ($membership->user) {
            $membership->user->notify(new \App\Notifications\GeneralComsocNotification(
                title: "Membership Application Update",
                message: "Your application could not be verified. Reason: {$data['rejection_reason']}. You may re-apply with updated proof of payment.",
                actionUrl: route('membership.index'),
                type: 'danger'
            ));
        }

        return back()->with('success', "Membership application for {$membership->user->name} has been rejected.");
    }
}

