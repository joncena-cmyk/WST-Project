<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MembershipController extends Controller
{
    /** Member views their own membership status */
    public function index(): View
    {
        $user        = Auth::user();
        $memberships = $user->memberships()
            ->with('program', 'approvedBy')
            ->latest()
            ->get();

        $active = $memberships->firstWhere('status', Membership::STATUS_ACTIVE);

        return view('membership.index', compact('memberships', 'active'));
    }
}
