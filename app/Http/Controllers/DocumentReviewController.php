<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocumentReviewController extends Controller
{
    private function authorizeAdviser(): void
    {
        abort_unless(
            Auth::check() && (Auth::user()->isAdviser() || Auth::user()->isAdmin()),
            403,
            'Only the Adviser can review organization documents.'
        );
    }

    /**
     * Adviser document review console.
     */
    public function index(Request $request): View
    {
        $this->authorizeAdviser();

        // Pending queue
        $pending = Document::with(['uploader', 'event'])
            ->whereIn('status', [Document::STATUS_SUBMITTED, Document::STATUS_UNDER_REVIEW])
            ->latest('submitted_at')
            ->get();

        // Review history
        $history = DocumentReview::with(['document.uploader', 'document.event', 'reviewer'])
            ->latest()
            ->paginate(15);

        return view('documents.review.index', compact('pending', 'history'));
    }

    /**
     * Show single document review interface.
     */
    public function show(Document $document): View
    {
        $this->authorizeAdviser();

        // Mark as under review if submitted
        if ($document->status === Document::STATUS_SUBMITTED) {
            $document->update(['status' => Document::STATUS_UNDER_REVIEW]);
        }

        $document->load(['uploader', 'event', 'reviews.reviewer']);

        return view('documents.review.show', compact('document'));
    }

    /**
     * Process review decision (approve, return with remarks, reject).
     */
    public function review(Request $request, Document $document): RedirectResponse
    {
        $this->authorizeAdviser();

        abort_unless(
            in_array($document->status, [Document::STATUS_SUBMITTED, Document::STATUS_UNDER_REVIEW]),
            422,
            'This document is not currently awaiting review.'
        );

        $data = $request->validate([
            'action'  => ['required', 'in:approved,returned,rejected'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $statusMap = [
            DocumentReview::ACTION_APPROVED => Document::STATUS_APPROVED,
            DocumentReview::ACTION_RETURNED => Document::STATUS_RETURNED,
            DocumentReview::ACTION_REJECTED => Document::STATUS_REJECTED,
        ];

        $updatePayload = ['status' => $statusMap[$data['action']]];
        if ($data['action'] === DocumentReview::ACTION_APPROVED) {
            $updatePayload['approved_at'] = now();
        }

        $document->update($updatePayload);

        // Record audit review
        DocumentReview::create([
            'document_id' => $document->id,
            'reviewed_by' => Auth::id(),
            'action'      => $data['action'],
            'remarks'     => $data['remarks'] ?? null,
        ]);

        $messages = [
            'approved' => 'Document marked as Approved.',
            'returned' => 'Document returned to officer for revisions.',
            'rejected' => 'Document marked as Rejected.',
        ];

        return redirect()->route('documents.review.index')
            ->with('success', $messages[$data['action']]);
    }
}
