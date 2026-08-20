<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ModerationController extends Controller
{
    private function authorizeOfficer(): void
    {
        abort_unless(Auth::check() && Auth::user()->isOfficerOrAbove(), 403);
    }

    /** Pending reports dashboard */
    public function index(): View
    {
        $this->authorizeOfficer();

        $reports = Report::with(['reporter', 'reportable', 'reviewer'])
            ->where('status', Report::STATUS_PENDING)
            ->latest()
            ->paginate(20);

        return view('moderation.index', compact('reports'));
    }

    /** Resolve a report */
    public function resolve(Request $request, Report $report): RedirectResponse
    {
        $this->authorizeOfficer();

        $data = $request->validate([
            'action'           => ['required', 'in:resolve,dismiss'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
            'hide_content'     => ['boolean'],
        ]);

        if ($request->boolean('hide_content')) {
            $reportable = $report->reportable;
            if ($reportable instanceof Post) {
                $reportable->update([
                    'is_hidden' => true,
                    'hidden_at' => now(),
                    'hidden_by' => Auth::id(),
                ]);
            } elseif ($reportable instanceof Comment) {
                $reportable->update([
                    'is_hidden' => true,
                    'hidden_at' => now(),
                    'hidden_by' => Auth::id(),
                ]);
            }
        }

        $report->update([
            'status'           => $data['action'] === 'resolve' ? Report::STATUS_RESOLVED : Report::STATUS_DISMISSED,
            'reviewed_by'      => Auth::id(),
            'reviewed_at'      => now(),
            'resolution_notes' => $data['resolution_notes'] ?? null,
        ]);

        return redirect()->route('moderation.index')
            ->with('success', 'Report ' . $data['action'] . 'd.');
    }
}
