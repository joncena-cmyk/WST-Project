<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reportable_type' => ['required', 'in:post,comment'],
            'reportable_id'   => ['required', 'integer'],
            'reason'          => ['required', 'in:spam,inappropriate,harassment,other'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        $modelClass = $data['reportable_type'] === 'post' ? Post::class : Comment::class;
        $reportable = $modelClass::findOrFail($data['reportable_id']);

        // Prevent duplicate reports from same user
        $alreadyReported = Report::where('reporter_id', Auth::id())
            ->where('reportable_type', $modelClass)
            ->where('reportable_id', $reportable->id)
            ->where('status', Report::STATUS_PENDING)
            ->exists();

        if ($alreadyReported) {
            return back()->with('info', 'You have already reported this content.');
        }

        Report::create([
            'reporter_id'     => Auth::id(),
            'reportable_type' => $modelClass,
            'reportable_id'   => $reportable->id,
            'reason'          => $data['reason'],
            'notes'           => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Report submitted. Our moderators will review it.');
    }
}
