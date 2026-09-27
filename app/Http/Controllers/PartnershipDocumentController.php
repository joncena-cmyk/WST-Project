<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Partnership;
use App\Models\PartnershipDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PartnershipDocumentController extends Controller
{
    public function store(Request $request, Partnership $partnership): RedirectResponse
    {
        Gate::authorize('update', $partnership);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'document_type' => 'required|in:moa_mou,letter_of_intent,sponsorship_deck,invitation,completion_report,other',
            'file'          => 'required|file|mimes:pdf,docx,doc,zip,png,jpg,jpeg|max:10240', // 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $size = $file->getSize();
        $path = $file->store('partnership_documents');

        $document = $partnership->documents()->create([
            'title'         => $validated['title'],
            'document_type' => $validated['document_type'],
            'file_path'     => $path,
            'file_name'     => $originalName,
            'file_size'     => $size,
            'uploaded_by'   => Auth::id(),
        ]);

        AuditLog::record('partnership_document_uploaded', "Uploaded document '{$document->title}' for partnership '{$partnership->title}'.", $document);

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', "Document '{$document->title}' uploaded successfully.");
    }

    public function download(PartnershipDocument $document): StreamedResponse
    {
        Gate::authorize('view', $document->partnership);

        abort_unless(Storage::exists($document->file_path), 404, 'File not found in secure storage.');

        AuditLog::record('partnership_document_downloaded', "Downloaded document '{$document->title}'.", $document);

        return Storage::download($document->file_path, $document->file_name);
    }

    public function destroy(PartnershipDocument $document): RedirectResponse
    {
        Gate::authorize('update', $document->partnership);

        $title = $document->title;
        $partnership = $document->partnership;

        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $document->delete();

        AuditLog::record('partnership_document_deleted', "Deleted document '{$title}' from partnership '{$partnership->title}'.");

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', "Document '{$title}' deleted.");
    }
}
