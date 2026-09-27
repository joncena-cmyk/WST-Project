<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Program;
use App\Models\User;
use App\Notifications\GeneralComsocNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MembershipController extends Controller
{
    /** Member views their own membership status & application form */
    public function index(): View
    {
        $user        = Auth::user();
        $memberships = $user->memberships()
            ->with('program', 'approvedBy')
            ->latest()
            ->get();

        $active  = $memberships->firstWhere('status', Membership::STATUS_ACTIVE);
        $pending = $memberships->firstWhere('status', Membership::STATUS_PENDING);
        $programs = Program::where('is_active', true)->orderBy('name')->get();

        return view('membership.index', compact('memberships', 'active', 'pending', 'programs'));
    }

    /** Student submits online membership application with payment receipt */
    public function apply(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Check if user already has an active or pending membership
        if ($user->activeMembership()) {
            return back()->with('error', 'You already have an active COMSOC membership.');
        }

        if ($user->memberships()->where('status', Membership::STATUS_PENDING)->exists()) {
            return back()->with('error', 'You already have a pending membership application under review.');
        }

        $validated = $request->validate([
            'program_id'        => ['required', 'exists:programs,id'],
            'payment_method'    => ['required', 'in:gcash,cash,bank_transfer,free'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'proof_of_payment'  => ['required_unless:payment_method,free', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofPath = $request->file('proof_of_payment')->store('membership_receipts', 'public');
        }

        // Update user's program if not set
        if (!$user->program_id) {
            $user->update(['program_id' => $validated['program_id']]);
        }

        $membership = Membership::create([
            'user_id'               => $user->id,
            'program_id'            => $validated['program_id'],
            'academic_year'         => '2026-2027',
            'status'                => Membership::STATUS_PENDING,
            'payment_method'        => $validated['payment_method'],
            'payment_reference'     => $validated['payment_reference'] ?? null,
            'payment_amount'        => 50.00,
            'proof_of_payment_path' => $proofPath,
            'notes'                 => $validated['notes'] ?? null,
        ]);

        // Notify Treasurers and Admins
        $officers = User::whereIn('role', [User::ROLE_TREASURER, User::ROLE_PRESIDENT, User::ROLE_ADMIN])
            ->where('account_status', User::STATUS_ACTIVE)
            ->get();

        foreach ($officers as $officer) {
            $officer->notify(new GeneralComsocNotification(
                title: "New Membership Application: {$user->name}",
                message: "{$user->name} submitted a membership application ({$membership->payment_method}) for review.",
                actionUrl: route('admin.memberships.index', ['status' => 'pending']),
                type: 'info'
            ));
        }

        return redirect()->route('membership.index')
            ->with('success', 'Your membership application and proof of payment have been submitted! The Treasurer will review and verify your payment.');
    }
}
