<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CollaborationActivity;
use App\Models\Partnership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CollaborationActivityController extends Controller
{
    public function store(Request $request, Partnership $partnership): RedirectResponse
    {
        Gate::authorize('update', $partnership);

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'activity_type'   => 'required|in:workshop,hackathon,seminar,competition,project,training,community',
            'activity_date'   => 'nullable|date',
            'venue'           => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'event_id'        => 'nullable|exists:events,id',
            'lead_officer_id' => 'nullable|exists:users,id',
        ]);

        $activity = $partnership->activities()->create([
            ...$validated,
            'status' => CollaborationActivity::STATUS_PLANNED,
        ]);

        AuditLog::record('collaboration_activity_created', "Added technical activity '{$activity->title}' to partnership '{$partnership->title}'.", $activity);

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', "Activity '{$activity->title}' added to partnership schedule.");
    }

    public function updateStatus(Request $request, CollaborationActivity $activity): RedirectResponse
    {
        Gate::authorize('update', $activity->partnership);

        $validated = $request->validate([
            'status' => 'required|in:planned,ongoing,completed,cancelled',
        ]);

        $activity->update(['status' => $validated['status']]);

        AuditLog::record('collaboration_activity_status', "Activity '{$activity->title}' marked as {$validated['status']}.", $activity);

        return redirect()->back()
            ->with('success', "Activity status updated to " . ucfirst($validated['status']) . ".");
    }

    public function destroy(CollaborationActivity $activity): RedirectResponse
    {
        Gate::authorize('update', $activity->partnership);

        $title = $activity->title;
        $partnership = $activity->partnership;
        $activity->delete();

        AuditLog::record('collaboration_activity_deleted', "Removed activity '{$title}' from partnership '{$partnership->title}'.");

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', "Activity '{$title}' removed.");
    }
}
