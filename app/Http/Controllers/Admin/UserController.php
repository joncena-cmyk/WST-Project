<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403, 'Only system administrators can access user management.');
    }

    /** List all users with search/filter */
    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $query = User::with('program')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->get('status')) {
            $query->where('account_status', $status);
        }

        if ($program = $request->get('program_id')) {
            $query->where('program_id', $program);
        }

        $users    = $query->paginate(20)->withQueryString();
        $programs = Program::where('is_active', true)->orderBy('short_name')->get();

        return view('admin.users.index', compact('users', 'programs'));
    }

    /** Show single user detail */
    public function show(User $user): View
    {
        $this->authorizeAdmin();
        $user->load(['program', 'memberships.program']);
        $programs = Program::where('is_active', true)->orderBy('short_name')->get();
        return view('admin.users.show', compact('user', 'programs'));
    }

    /** Update role and account status */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        // Prevent admin from demoting themselves
        if ($user->id === Auth::id() && $request->role !== User::ROLE_ADMIN) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $data = $request->validate([
            'role'           => ['required', Rule::in(array_keys(User::ROLES))],
            'account_status' => ['required', Rule::in([
                User::STATUS_ACTIVE,
                User::STATUS_SUSPENDED,
                User::STATUS_INACTIVE,
            ])],
            'student_id'     => ['nullable', 'string', 'max:20', Rule::unique('users', 'student_id')->ignore($user->id)],
            'program_id'     => ['nullable', 'exists:programs,id'],
        ]);

        $user->update($data);

        return back()->with('success', "User {$user->name} updated successfully.");
    }
}
