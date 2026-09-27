<?php

namespace App\Http\Controllers;

use App\Mail\PartnershipProposalMail;
use App\Models\AuditLog;
use App\Models\Committee;
use App\Models\Document;
use App\Models\DocumentReview;
use App\Models\Event;
use App\Models\EventTask;
use App\Models\PartnerOrganization;
use App\Models\Partnership;
use App\Models\User;
use App\Notifications\GeneralComsocNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PartnershipController extends Controller
{
    public function index(Request $request): View
    {
        $query = Partnership::with(['partner', 'creator', 'activities', 'documents'])
            ->withCount(['activities', 'documents'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        $partnerships = $query->paginate(10)->withQueryString();

        $activeCount = Partnership::where('status', Partnership::STATUS_ACTIVE)->count();
        $underReviewCount = Partnership::where('status', Partnership::STATUS_UNDER_REVIEW)->count();
        $partnerOrgsCount = PartnerOrganization::where('status', PartnerOrganization::STATUS_ACTIVE)->count();

        $partners = PartnerOrganization::where('status', PartnerOrganization::STATUS_ACTIVE)->orderBy('name')->get();

        return view('partnerships.index', compact('partnerships', 'activeCount', 'underReviewCount', 'partnerOrgsCount', 'partners'));
    }

    public function create(): View
    {
        Gate::authorize('create', Partnership::class);

        $partners = PartnerOrganization::where('status', PartnerOrganization::STATUS_ACTIVE)->orderBy('name')->get();

        return view('partnerships.create', compact('partners'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Partnership::class);

        $validated = $request->validate([
            'partner_id'         => 'required|exists:partner_organizations,id',
            'partnership_type'   => 'nullable|string|in:co_organizer,technical_support,resource_speakers,sponsorship',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'academic_year'      => 'required|string|max:20',
            'sponsorship_amount' => 'nullable|numeric|min:0',
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date|after_or_equal:start_date',
        ]);

        $partnership = Partnership::create([
            ...$validated,
            'partnership_type' => $validated['partnership_type'] ?? Partnership::TYPE_CO_ORGANIZER,
            'status'           => Partnership::STATUS_PROPOSED,
            'created_by'       => Auth::id(),
        ]);

        $partner = $partnership->partner;
        $emailSentNote = '';
        if ($partner && !empty($partner->contact_email)) {
            try {
                Mail::to($partner->contact_email)->send(new PartnershipProposalMail($partnership));
                AuditLog::record('partnership_email_sent', "Emailed formal proposal to {$partner->name} ({$partner->contact_email}).", $partnership);
                $emailSentNote = " Proposal also emailed to {$partner->contact_email}.";
            } catch (\Throwable $e) {
                Log::warning("Failed to send partnership email to {$partner->contact_email}: " . $e->getMessage());
            }
        }

        AuditLog::record('partnership_created', "Proposed new partnership '{$partnership->title}' ({$partnership->getPartnershipTypeLabel()}) with {$partnership->partner->name}.", $partnership);

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', 'Partnership proposal created successfully.' . $emailSentNote);
    }

    public function show(Partnership $partnership): View
    {
        $partnership->load([
            'partner.assignedOfficer',
            'creator',
            'reviewer',
            'endorser',
            'event',
            'proposalDocument',
            'activities.leadOfficer',
            'activities.event',
            'partnershipDocuments.uploader',
            'documents.uploader',
        ]);

        $events = Event::orderBy('event_date', 'desc')->get();
        $officers = User::officers()->get();

        return view('partnerships.show', compact('partnership', 'events', 'officers'));
    }

    public function edit(Partnership $partnership): View
    {
        Gate::authorize('update', $partnership);

        $partners = PartnerOrganization::where('status', PartnerOrganization::STATUS_ACTIVE)->orderBy('name')->get();

        return view('partnerships.edit', compact('partnership', 'partners'));
    }

    public function update(Request $request, Partnership $partnership): RedirectResponse
    {
        Gate::authorize('update', $partnership);

        $validated = $request->validate([
            'partner_id'         => 'required|exists:partner_organizations,id',
            'partnership_type'   => 'nullable|string|in:co_organizer,technical_support,resource_speakers,sponsorship',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'academic_year'      => 'required|string|max:20',
            'sponsorship_amount' => 'nullable|numeric|min:0',
            'start_date'         => 'nullable|date',
            'end_date'           => 'nullable|date|after_or_equal:start_date',
        ]);

        $partnership->update($validated);

        AuditLog::record('partnership_updated', "Updated details of partnership '{$partnership->title}'.", $partnership);

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', 'Partnership details updated.');
    }

    public function saveLetter(Request $request, Partnership $partnership): RedirectResponse
    {
        Gate::authorize('update', $partnership);

        $validated = $request->validate([
            'letter_content'     => 'required|string',
            'sponsorship_amount' => 'nullable|numeric|min:0',
        ]);

        $partnership->update([
            'letter_content'     => $validated['letter_content'],
            'sponsorship_amount' => $validated['sponsorship_amount'] ?? 0,
        ]);

        // Sync or create proposal document record
        if (!$partnership->proposal_document_id) {
            $doc = Document::create([
                'title'          => "Letter of Intent & Proposal - {$partnership->title}",
                'category'       => Document::CATEGORY_INTENT,
                'partnership_id' => $partnership->id,
                'uploaded_by'    => Auth::id(),
                'file_path'      => 'documents/letters/letter_' . $partnership->id . '.txt',
                'file_name'      => 'letter_of_intent_' . $partnership->id . '.txt',
                'file_size'      => strlen($validated['letter_content']),
                'file_mime'      => 'text/plain',
                'status'         => Document::STATUS_DRAFT,
                'notes'          => "Auto-generated formal Letter of Intent for {$partnership->partner->name}.",
                'version'        => 1,
            ]);

            $partnership->update(['proposal_document_id' => $doc->id]);
        } else {
            $partnership->proposalDocument?->update([
                'file_size' => strlen($validated['letter_content']),
                'title'     => "Letter of Intent & Proposal - {$partnership->title}",
            ]);
        }

        AuditLog::record('partnership_letter_updated', "Updated official Letter of Intent for partnership '{$partnership->title}'.", $partnership);

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', 'Letter of Intent & Proposal draft saved successfully.');
    }

    public function submitToAdviser(Request $request, Partnership $partnership): RedirectResponse
    {
        Gate::authorize('update', $partnership);

        if (empty($partnership->letter_content)) {
            $partnership->update(['letter_content' => $partnership->getDefaultLetterContent()]);
        }

        // Ensure proposal document exists
        if (!$partnership->proposal_document_id) {
            $doc = Document::create([
                'title'          => "Letter of Intent & Proposal - {$partnership->title}",
                'category'       => Document::CATEGORY_INTENT,
                'partnership_id' => $partnership->id,
                'uploaded_by'    => Auth::id(),
                'file_path'      => 'documents/letters/letter_' . $partnership->id . '.txt',
                'file_name'      => 'letter_of_intent_' . $partnership->id . '.txt',
                'file_size'      => strlen($partnership->letter_content),
                'file_mime'      => 'text/plain',
                'status'         => Document::STATUS_SUBMITTED,
                'submitted_at'   => now(),
                'notes'          => "Submitted formal Letter of Intent for {$partnership->partner->name}.",
                'version'        => 1,
            ]);

            $partnership->update(['proposal_document_id' => $doc->id]);
        } else {
            $partnership->proposalDocument?->update([
                'status'       => Document::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);
        }

        $oldStatus = $partnership->status;
        $partnership->update([
            'status' => Partnership::STATUS_UNDER_REVIEW,
        ]);

        // Send notifications to all Academic Advisers and Admins
        $advisers = User::whereIn('role', ['adviser', 'admin'])->get();
        foreach ($advisers as $adv) {
            $adv->notify(new GeneralComsocNotification(
                'Partnership Collaboration Awaiting Endorsement',
                "{$partnership->partner->name} proposal '{$partnership->title}' has been submitted for your formal endorsement.",
                route('partnerships.show', $partnership),
                'info',
                'briefcase'
            ));
        }

        AuditLog::record(
            'partnership_submitted_adviser',
            "Submitted partnership '{$partnership->title}' with {$partnership->partner->name} to Academic Adviser for endorsement.",
            $partnership,
            ['old_status' => $oldStatus, 'new_status' => Partnership::STATUS_UNDER_REVIEW]
        );

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', 'Collaboration proposal and Letter of Intent submitted to Academic Adviser.');
    }

    public function endorse(Request $request, Partnership $partnership): RedirectResponse
    {
        $user = Auth::user();
        abort_unless(
            $user->isAdviser() || $user->isAdmin() || $user->isPresident(),
            403,
            'Only the Academic Adviser, President, or Admin can endorse partnerships.'
        );

        $validated = $request->validate([
            'remarks' => 'nullable|string|max:2000',
        ]);

        $partnership->update([
            'status'              => Partnership::STATUS_APPROVED,
            'endorsed_by'         => $user->id,
            'endorsed_at'         => now(),
            'endorsement_remarks' => $validated['remarks'] ?? 'Formally endorsed and authorized for COMSOC joint execution.',
            'reviewed_by'         => $user->id,
            'reviewed_at'         => now(),
            'review_remarks'      => $validated['remarks'] ?? 'Approved by Academic Adviser.',
        ]);

        // Approve linked document if any
        if ($partnership->proposalDocument) {
            $partnership->proposalDocument->update([
                'status'      => Document::STATUS_APPROVED,
                'approved_at' => now(),
            ]);

            DocumentReview::create([
                'document_id' => $partnership->proposalDocument->id,
                'reviewed_by' => $user->id,
                'action'      => 'approved',
                'remarks'     => $validated['remarks'] ?? 'Formally endorsed and approved by Academic Adviser.',
            ]);
        }

        // Notify officers
        $officers = User::officers()->get();
        foreach ($officers as $officer) {
            $officer->notify(new GeneralComsocNotification(
                'Partnership Formally Endorsed! 🎉',
                "The Academic Adviser has officially endorsed the partnership '{$partnership->title}' with {$partnership->partner->name}. You can now convert it to an official Joint Event.",
                route('partnerships.show', $partnership),
                'success',
                'badge-check'
            ));
        }

        AuditLog::record(
            'partnership_endorsed',
            "Academic Adviser endorsed partnership proposal '{$partnership->title}'.",
            $partnership,
            ['endorsed_by' => $user->name, 'remarks' => $validated['remarks'] ?? null]
        );

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', 'Partnership proposal formally endorsed and approved! You may now spawn the Joint Event.');
    }

    public function sendEmail(Request $request, Partnership $partnership): RedirectResponse
    {
        Gate::authorize('update', $partnership);

        $validated = $request->validate([
            'recipient_email' => 'nullable|email|max:255',
            'custom_message'  => 'nullable|string|max:2000',
        ]);

        $recipientEmail = $validated['recipient_email'] ?? $partnership->partner?->contact_email;

        if (empty($recipientEmail)) {
            return redirect()->route('partnerships.show', $partnership)
                ->with('error', 'Cannot send email: No contact email specified for this partner organization.');
        }

        try {
            Mail::to($recipientEmail)->send(new PartnershipProposalMail($partnership, $validated['custom_message'] ?? null));

            AuditLog::record(
                'partnership_email_sent',
                "Dispatched official Letter of Intent & Proposal email to {$partnership->partner->name} ({$recipientEmail}).",
                $partnership,
                ['recipient' => $recipientEmail, 'has_custom_message' => !empty($validated['custom_message'])]
            );

            return redirect()->route('partnerships.show', $partnership)
                ->with('success', "✉️ Official Letter of Intent & Proposal successfully emailed to {$recipientEmail}.");
        } catch (\Throwable $e) {
            Log::error("Failed to send partnership email to {$recipientEmail}: " . $e->getMessage());

            return redirect()->route('partnerships.show', $partnership)
                ->with('error', "Failed to dispatch email: {$e->getMessage()}");
        }
    }

    public function spawnEvent(Request $request, Partnership $partnership): RedirectResponse
    {
        $user = Auth::user();
        abort_unless(
            $user->isOfficerOrAbove() || $user->isAdviser() || $user->isAdmin(),
            403,
            'Unauthorized to spawn event.'
        );

        abort_if(
            !$partnership->canBeSpawnedToEvent(),
            422,
            'This partnership is either not approved or already has an active spawned event.'
        );

        $validated = $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'nullable|string',
            'event_date'            => 'required|date',
            'start_time'            => 'required',
            'end_time'              => 'nullable',
            'venue'                 => 'required|string|max:255',
            'participant_limit'     => 'nullable|integer|min:1',
            'registration_deadline' => 'nullable|date',
        ]);

        $event = Event::create([
            'created_by'            => $user->id,
            'partnership_id'        => $partnership->id,
            'partner_id'            => $partnership->partner_id,
            'is_collaboration'      => true,
            'title'                 => $validated['title'],
            'description'           => $validated['description'] ?? "Joint event co-presented with {$partnership->partner->name}.\n\n" . ($partnership->description ?? ''),
            'event_date'            => $validated['event_date'],
            'start_time'            => $validated['start_time'],
            'end_time'              => $validated['end_time'] ?? null,
            'venue'                 => $validated['venue'],
            'participant_limit'     => $validated['participant_limit'] ?? 150,
            'registration_deadline' => $validated['registration_deadline'] ?? null,
            'status'                => Event::STATUS_PUBLISHED,
            'published_at'          => now(),
        ]);

        // Link back to partnership
        $partnership->update([
            'event_id' => $event->id,
            'status'   => Partnership::STATUS_ACTIVE,
        ]);

        // Link proposal document to event
        if ($partnership->proposalDocument) {
            $partnership->proposalDocument->update(['event_id' => $event->id]);
        }

        // Seed default event committees and operational tasks based on partnership type
        $isTechSupport = $partnership->partnership_type === Partnership::TYPE_TECHNICAL_SUPPORT;

        $logistics = Committee::create([
            'event_id'     => $event->id,
            'name'         => $isTechSupport ? 'Technical Operations & Audio-Visual' : 'Logistics & Facilities',
            'description'  => $isTechSupport
                ? "Manages sound system, microphones, projector, and live broadcast setup for {$partnership->partner->name}."
                : "Handles room reservation, physical permits, and equipment setup for {$partnership->partner->name} joint event.",
            'head_user_id' => $user->id,
            'created_by'   => $user->id,
        ]);

        EventTask::create([
            'event_id'     => $event->id,
            'committee_id' => $logistics->id,
            'assigned_to'  => $user->id,
            'created_by'   => $user->id,
            'title'        => $isTechSupport ? 'Setup Stage Audio-Visual, Microphones & Live Streaming Equipment' : 'Secure University Facility Permit & Audio-Visual Equipment',
            'description'  => $isTechSupport
                ? "Deploy technical crew to setup microphones, projectors, sound mixer, and Zoom live stream broadcast for {$partnership->partner->name} event."
                : "Coordinate with UMak Admin and partner organization ({$partnership->partner->name}) for stage, sound, and venue requirements.",
            'status'       => 'pending',
            'priority'     => 'high',
            'due_date'     => now()->addDays(5),
        ]);

        $programs = Committee::create([
            'event_id'     => $event->id,
            'name'         => $isTechSupport ? 'Technical Secretariat & Stage Cueing' : 'Programs & Technical Support',
            'description'  => $isTechSupport
                ? "Oversees presentation decks, speaker timers, technical cueing, and live stream overlays."
                : "Oversees stage agenda, speaker coordination, and tech support with {$partnership->partner->name}.",
            'head_user_id' => $user->id,
            'created_by'   => $user->id,
        ]);

        EventTask::create([
            'event_id'     => $event->id,
            'committee_id' => $programs->id,
            'assigned_to'  => $user->id,
            'created_by'   => $user->id,
            'title'        => $isTechSupport ? 'Receive & Test Presentation Slides and Speaker Audio Feeds' : 'Finalize Program Flow & Resource Speaker Briefing',
            'description'  => $isTechSupport
                ? "Coordinate with {$partnership->partner->name} organizers for deck submissions, video clips, and technical run-through."
                : "Review presentation deck and technical requirements with {$partnership->partner->name} facilitators.",
            'status'       => 'pending',
            'priority'     => 'high',
            'due_date'     => now()->addDays(7),
        ]);

        $marketing = Committee::create([
            'event_id'     => $event->id,
            'name'         => $isTechSupport ? 'Technical Documentation & Live Media' : 'Creatives & Promotions',
            'description'  => $isTechSupport
                ? "Records event footage, technical photo documentation, and stream recordings for archival."
                : 'Handles co-branded publication materials, banners, and CCIS announcements.',
            'head_user_id' => $user->id,
            'created_by'   => $user->id,
        ]);

        EventTask::create([
            'event_id'     => $event->id,
            'committee_id' => $marketing->id,
            'assigned_to'  => $user->id,
            'created_by'   => $user->id,
            'title'        => $isTechSupport ? 'Record Master Audio/Video Feed & Archive Event Media' : 'Release Co-Branded Promotional Materials & Registration Link',
            'description'  => $isTechSupport
                ? "Archive high-definition video recording and technical photos for {$partnership->partner->name} and COMSOC documentation."
                : "Publish announcements featuring {$partnership->partner->name} logo and open registration to CCIS students.",
            'status'       => 'pending',
            'priority'     => 'medium',
            'due_date'     => now()->addDays(3),
        ]);

        // Notify all users
        $allUsers = User::all();
        foreach ($allUsers as $u) {
            $u->notify(new GeneralComsocNotification(
                'New Joint Event Published! 🚀',
                "COMSOC x {$partnership->partner->name} presents '{$event->title}'. Registration is now open!",
                route('events.show', $event),
                'success',
                'sparkles'
            ));
        }

        AuditLog::record(
            'partnership_event_spawned',
            "Spawned official joint event '{$event->title}' from partnership with {$partnership->partner->name}.",
            $event
        );

        return redirect()->route('events.show', $event)
            ->with('success', "🎉 Official Joint Event '{$event->title}' spawned successfully with automated committee task delegation!");
    }

    public function transitionStatus(Request $request, Partnership $partnership): RedirectResponse
    {
        $validated = $request->validate([
            'status'  => 'required|in:proposed,under_review,approved,active,completed,archived,declined',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $newStatus = $validated['status'];

        // If transitioning to approved/declined, ensure authorized reviewer
        if (in_array($newStatus, [Partnership::STATUS_APPROVED, Partnership::STATUS_DECLINED])) {
            Gate::authorize('review', $partnership);
            $partnership->reviewed_by = Auth::id();
            $partnership->reviewed_at = now();
            $partnership->review_remarks = $validated['remarks'] ?? null;
        } else {
            Gate::authorize('update', $partnership);
        }

        $oldStatus = $partnership->status;
        $partnership->status = $newStatus;
        $partnership->save();

        AuditLog::record(
            'partnership_status_changed',
            "Changed partnership '{$partnership->title}' status from {$oldStatus} to {$newStatus}.",
            $partnership,
            ['old_status' => $oldStatus, 'new_status' => $newStatus, 'remarks' => $validated['remarks'] ?? null]
        );

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', "Partnership status updated to {$partnership->getStatusLabel()}.");
    }

    public function destroy(Partnership $partnership): RedirectResponse
    {
        Gate::authorize('delete', $partnership);

        $title = $partnership->title;
        $partnership->delete();

        AuditLog::record('partnership_deleted', "Deleted partnership '{$title}'.");

        return redirect()->route('partnerships.index')
            ->with('success', 'Partnership deleted.');
    }

    public function showRespond(Partnership $partnership, string $token): View
    {
        abort_unless($partnership->response_token && hash_equals($partnership->response_token, $token), 404);

        $partnership->load(['partner', 'creator', 'endorser', 'proposalDocument', 'activities']);

        return view('partnerships.respond', compact('partnership', 'token'));
    }

    public function submitRespond(Request $request, Partnership $partnership, string $token): RedirectResponse
    {
        abort_unless($partnership->response_token && hash_equals($partnership->response_token, $token), 404);

        $validated = $request->validate([
            'response_status' => 'required|in:accepted,changes_requested,declined',
            'responder_name'  => 'required|string|max:255',
            'responder_title' => 'nullable|string|max:255',
            'remarks'         => 'nullable|string|max:3000',
            'signed_document' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
        ]);

        $partnership->update([
            'partner_response_status'  => $validated['response_status'],
            'partner_responder_name'   => $validated['responder_name'],
            'partner_responder_title'  => $validated['responder_title'] ?? null,
            'partner_response_remarks' => $validated['remarks'] ?? null,
            'partner_responded_at'     => now(),
        ]);

        // Advance partnership status
        if ($validated['response_status'] === 'accepted' && $partnership->status === Partnership::STATUS_PROPOSED) {
            $partnership->update(['status' => Partnership::STATUS_UNDER_REVIEW]);
        } elseif ($validated['response_status'] === 'declined') {
            $partnership->update(['status' => Partnership::STATUS_DECLINED]);
        }

        // Handle optional uploaded signed document
        if ($request->hasFile('signed_document')) {
            $file = $request->file('signed_document');
            $path = $file->store('partnerships/documents', 'public');

            $partnership->documents()->create([
                'title'       => "Partner Signed Agreement - {$validated['responder_name']}",
                'file_path'   => $path,
                'file_name'   => $file->getClientOriginalName(),
                'file_size'   => $file->getSize(),
                'mime_type'   => $file->getMimeType(),
                'uploaded_by' => $partnership->created_by,
                'description' => "Uploaded by partner representative {$validated['responder_name']} ({$validated['responder_title']}) via online response portal.",
            ]);
        }

        // Notify officers and advisers
        $officersAndAdvisers = User::whereIn('role', [
            User::ROLE_VP_EXTERNAL,
            User::ROLE_PRESIDENT,
            User::ROLE_ADVISER,
            User::ROLE_ADMIN,
        ])->get();

        $actionLabel = match ($validated['response_status']) {
            'accepted'          => 'Accepted & Signed 🎉',
            'changes_requested' => 'Requested Changes 📝',
            'declined'          => 'Declined ❌',
            default             => 'Responded to',
        };

        foreach ($officersAndAdvisers as $user) {
            $user->notify(new GeneralComsocNotification(
                "Partner {$actionLabel} Collaboration Proposal!",
                "{$partnership->partner->name} ({$validated['responder_name']}) has {$validated['response_status']} the proposal for '{$partnership->title}'.",
                route('partnerships.show', $partnership),
                $validated['response_status'] === 'accepted' ? 'success' : 'warning',
                'briefcase'
            ));
        }

        AuditLog::record(
            'partnership_partner_responded',
            "Partner {$partnership->partner->name} ({$validated['responder_name']}) {$validated['response_status']} the collaboration proposal.",
            $partnership,
            [
                'status'    => $validated['response_status'],
                'responder' => $validated['responder_name'],
                'title'     => $validated['responder_title'] ?? null,
                'remarks'   => $validated['remarks'] ?? null,
            ]
        );

        $flashMessage = match ($validated['response_status']) {
            'accepted'          => 'Thank you! Your official acceptance and confirmation has been transmitted to the COMSOC Executive Board and Academic Adviser.',
            'changes_requested' => 'Thank you! Your feedback and requested revisions have been forwarded to the COMSOC Executive Board.',
            'declined'          => 'Your response has been noted and submitted to the COMSOC Executive Board.',
            default             => 'Response submitted successfully.',
        };

        return redirect()->route('partnerships.respond', ['partnership' => $partnership->id, 'token' => $token])
            ->with('success', $flashMessage);
    }
}
