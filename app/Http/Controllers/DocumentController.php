<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Document directory hub with category, status, and event filters.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Document::class);

        $query = Document::with(['uploader', 'event', 'latestReview.reviewer'])->latest();

        // Scope to user's uploaded documents if requested
        if ($request->filled('mine') && $request->boolean('mine')) {
            $query->where('uploaded_by', Auth::id());
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Event filter
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->input('event_id'));
        }

        // Search by title or notes
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(15)->withQueryString();

        $categories = Document::categories();
        $events     = Event::orderBy('event_date', 'desc')->get(['id', 'title', 'event_date']);

        // Summary counts for filter tabs
        $counts = [
            'all'       => Document::count(),
            'mine'      => Document::where('uploaded_by', Auth::id())->count(),
            'approved'  => Document::where('status', Document::STATUS_APPROVED)->count(),
            'pending'   => Document::whereIn('status', [Document::STATUS_SUBMITTED, Document::STATUS_UNDER_REVIEW])->count(),
            'returned'  => Document::where('status', Document::STATUS_RETURNED)->count(),
        ];

        return view('documents.index', compact('documents', 'categories', 'events', 'counts'));
    }

    /**
     * Show document creation / upload form.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Document::class);

        $categories    = Document::categories();
        $events        = Event::orderBy('event_date', 'desc')->get(['id', 'title', 'event_date']);
        $selectedEvent = $request->filled('event_id') ? Event::find($request->input('event_id')) : null;

        return view('documents.create', compact('categories', 'events', 'selectedEvent'));
    }

    /**
     * Store new document in private storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Document::class);

        $data = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', array_keys(Document::categories()))],
            'event_id' => ['nullable', 'exists:events,id'],
            'file'     => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,webp', 'max:15360'], // 15MB max
            'notes'    => ['nullable', 'string', 'max:2000'],
        ]);

        $file     = $request->file('file');
        $filePath = $file->store('documents', 'local'); // Private storage (storage/app/documents)

        $document = Document::create([
            'title'        => $data['title'],
            'category'     => $data['category'],
            'event_id'     => $data['event_id'] ?? null,
            'uploaded_by'  => Auth::id(),
            'file_path'    => $filePath,
            'file_name'    => $file->getClientOriginalName(),
            'file_size'    => $file->getSize(),
            'file_mime'    => $file->getClientMimeType(),
            'status'       => Document::STATUS_DRAFT,
            'notes'        => $data['notes'] ?? null,
            'version'      => 1,
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document uploaded as draft. Click "Submit for Review" when ready.');
    }

    /**
     * Show single document workspace with review history.
     */
    public function show(Document $document): View
    {
        $this->authorize('view', $document);
        $document->load(['uploader', 'event', 'reviews.reviewer']);

        return view('documents.show', compact('document'));
    }

    /**
     * Show revision edit form (when document is draft or returned).
     */
    public function edit(Document $document): View
    {
        $this->authorize('update', $document);

        $categories = Document::categories();
        $events     = Event::orderBy('event_date', 'desc')->get(['id', 'title', 'event_date']);

        return view('documents.edit', compact('document', 'categories', 'events'));
    }

    /**
     * Update document metadata or replace with a new revised file.
     */
    public function update(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);

        $data = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', array_keys(Document::categories()))],
            'event_id' => ['nullable', 'exists:events,id'],
            'file'     => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,webp', 'max:15360'],
            'notes'    => ['nullable', 'string', 'max:2000'],
        ]);

        $updatePayload = [
            'title'    => $data['title'],
            'category' => $data['category'],
            'event_id' => $data['event_id'] ?? null,
            'notes'    => $data['notes'] ?? null,
        ];

        // If replacing the file
        if ($request->hasFile('file')) {
            // Delete old file from private storage
            if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
                Storage::disk('local')->delete($document->file_path);
            }

            $file = $request->file('file');
            $updatePayload['file_path'] = $file->store('documents', 'local');
            $updatePayload['file_name'] = $file->getClientOriginalName();
            $updatePayload['file_size'] = $file->getSize();
            $updatePayload['file_mime'] = $file->getClientMimeType();
            $updatePayload['version']   = $document->version + 1;
        }

        $document->update($updatePayload);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Submit document for Adviser review.
     */
    public function submit(Document $document): RedirectResponse
    {
        $this->authorize('submit', $document);

        $document->update([
            'status'       => Document::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document submitted to COMSOC Adviser for formal review.');
    }

    /**
     * Soft delete draft document.
     */
    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted.');
    }

    /**
     * Stream private document file to authorized user for download.
     */
    public function download(Document $document): StreamedResponse|RedirectResponse
    {
        $this->authorize('download', $document);

        if (!Storage::disk('local')->exists($document->file_path)) {
            return back()->with('error', 'File not found on private storage.');
        }

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }

    /**
     * In-browser preview for PDF / images.
     */
    public function preview(Document $document): StreamedResponse|RedirectResponse
    {
        $this->authorize('download', $document);

        if (!Storage::disk('local')->exists($document->file_path)) {
            return back()->with('error', 'File not found on private storage.');
        }

        return Storage::disk('local')->response($document->file_path, $document->file_name, [
            'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
        ]);
    }
}
