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
}
