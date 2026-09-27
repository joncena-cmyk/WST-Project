<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PartnerOrganization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PartnerOrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $query = PartnerOrganization::with(['assignedOfficer', 'partnerships'])
            ->withCount('partnerships')
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('contact_person', 'like', $search)
                  ->orWhere('contact_email', 'like', $search);
            });
        }

        $organizations = $query->paginate(12)->withQueryString();

        return view('partnerships.organizations.index', compact('organizations'));
    }

    public function create(): View
    {
        Gate::authorize('create', PartnerOrganization::class);

        $officers = User::officers()->get();

        return view('partnerships.organizations.create', compact('officers'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', PartnerOrganization::class);

        $validated = $request->validate([
            'name'                => 'required|string|max:255|unique:partner_organizations,name',
            'type'                => 'required|in:industry,academic,student_org,government,ngo',
            'contact_person'      => 'nullable|string|max:255',
            'contact_email'       => 'nullable|email|max:255',
            'contact_phone'       => 'nullable|string|max:50',
            'website'             => 'nullable|url|max:255',
            'description'         => 'nullable|string',
            'assigned_officer_id' => 'nullable|exists:users,id',
            'status'              => 'required|in:active,inactive',
        ]);

        $org = PartnerOrganization::create($validated);

        AuditLog::record('partner_org_created', "Registered partner organization '{$org->name}'.", $org);

        return redirect()->route('partner-organizations.index')
            ->with('success', "Partner organization '{$org->name}' created successfully.");
    }

    public function show(PartnerOrganization $partnerOrganization): View
    {
        $partnerOrganization->load([
            'assignedOfficer',
            'partnerships.activities',
            'partnerships.documents',
        ]);

        return view('partnerships.organizations.show', compact('partnerOrganization'));
    }

    public function edit(PartnerOrganization $partnerOrganization): View
    {
        Gate::authorize('update', $partnerOrganization);

        $officers = User::officers()->get();

        return view('partnerships.organizations.edit', compact('partnerOrganization', 'officers'));
    }

    public function update(Request $request, PartnerOrganization $partnerOrganization): RedirectResponse
    {
        Gate::authorize('update', $partnerOrganization);

        $validated = $request->validate([
            'name'                => 'required|string|max:255|unique:partner_organizations,name,' . $partnerOrganization->id,
            'type'                => 'required|in:industry,academic,student_org,government,ngo',
            'contact_person'      => 'nullable|string|max:255',
            'contact_email'       => 'nullable|email|max:255',
            'contact_phone'       => 'nullable|string|max:50',
            'website'             => 'nullable|url|max:255',
            'description'         => 'nullable|string',
            'assigned_officer_id' => 'nullable|exists:users,id',
            'status'              => 'required|in:active,inactive',
        ]);

        $partnerOrganization->update($validated);

        AuditLog::record('partner_org_updated', "Updated partner organization '{$partnerOrganization->name}'.", $partnerOrganization);

        return redirect()->route('partner-organizations.index')
            ->with('success', 'Partner organization updated successfully.');
    }

    public function destroy(PartnerOrganization $partnerOrganization): RedirectResponse
    {
        Gate::authorize('delete', $partnerOrganization);

        $name = $partnerOrganization->name;
        $partnerOrganization->delete();

        AuditLog::record('partner_org_deleted', "Deleted partner organization '{$name}'.");

        return redirect()->route('partner-organizations.index')
            ->with('success', "Partner organization '{$name}' deleted.");
    }
}
