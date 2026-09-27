<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partnership extends Model
{
    use HasFactory;

    const STATUS_PROPOSED     = 'proposed';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED     = 'approved';
    const STATUS_ACTIVE       = 'active';
    const STATUS_COMPLETED    = 'completed';
    const STATUS_ARCHIVED     = 'archived';
    const STATUS_DECLINED     = 'declined';

    const TYPE_CO_ORGANIZER       = 'co_organizer';
    const TYPE_TECHNICAL_SUPPORT  = 'technical_support';
    const TYPE_RESOURCE_SPEAKERS  = 'resource_speakers';
    const TYPE_SPONSORSHIP        = 'sponsorship';

    const RESPONSE_ACCEPTED          = 'accepted';
    const RESPONSE_CHANGES_REQUESTED = 'changes_requested';
    const RESPONSE_DECLINED          = 'declined';

    protected $fillable = [
        'partner_id',
        'partnership_type',
        'event_id',
        'proposal_document_id',
        'title',
        'description',
        'letter_content',
        'academic_year',
        'sponsorship_amount',
        'start_date',
        'end_date',
        'status',
        'response_token',
        'partner_response_status',
        'partner_responder_name',
        'partner_responder_title',
        'partner_response_remarks',
        'partner_responded_at',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'review_remarks',
        'endorsed_by',
        'endorsed_at',
        'endorsement_remarks',
    ];

    protected function casts(): array
    {
        return [
            'start_date'           => 'date',
            'end_date'             => 'date',
            'reviewed_at'          => 'datetime',
            'endorsed_at'          => 'datetime',
            'partner_responded_at' => 'datetime',
            'sponsorship_amount'   => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Partnership $partnership) {
            if (empty($partnership->response_token)) {
                $partnership->response_token = \Illuminate\Support\Str::random(40);
            }
        });
    }

    public function getResponseUrl(): string
    {
        if (empty($this->response_token)) {
            $this->response_token = \Illuminate\Support\Str::random(40);
            $this->saveQuietly();
        }

        return route('partnerships.respond', [
            'partnership' => $this->id,
            'token'       => $this->response_token,
        ]);
    }

    public function isPartnerAccepted(): bool
    {
        return $this->partner_response_status === self::RESPONSE_ACCEPTED;
    }

    public function getPartnerResponseStatusLabel(): string
    {
        return match ($this->partner_response_status) {
            self::RESPONSE_ACCEPTED          => 'Accepted & Signed',
            self::RESPONSE_CHANGES_REQUESTED => 'Revisions Requested',
            self::RESPONSE_DECLINED          => 'Declined by Partner',
            default                          => 'Awaiting Partner Response',
        };
    }

    public function getPartnerResponseStatusBadge(): string
    {
        return match ($this->partner_response_status) {
            self::RESPONSE_ACCEPTED          => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::RESPONSE_CHANGES_REQUESTED => 'bg-amber-100 text-amber-800 border-amber-200',
            self::RESPONSE_DECLINED          => 'bg-rose-100 text-rose-800 border-rose-200',
            default                          => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(PartnerOrganization::class, 'partner_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function proposalDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'proposal_document_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function endorser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'endorsed_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CollaborationActivity::class)->orderBy('activity_date', 'asc');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PartnershipDocument::class)->latest();
    }

    public function partnershipDocuments(): HasMany
    {
        return $this->hasMany(PartnershipDocument::class)->latest();
    }

    public function officialDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'partnership_id')->latest();
    }

    public function isEndorsed(): bool
    {
        return !is_null($this->endorsed_by) || $this->status === self::STATUS_APPROVED || $this->status === self::STATUS_ACTIVE;
    }

    public function canBeSubmittedToAdviser(): bool
    {
        return in_array($this->status, [self::STATUS_PROPOSED, self::STATUS_DECLINED]);
    }

    public function canBeSpawnedToEvent(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_ACTIVE]) && is_null($this->event_id);
    }

    public function getPartnershipTypeLabel(): string
    {
        return match ($this->partnership_type) {
            self::TYPE_TECHNICAL_SUPPORT => 'Technical Support & Operations Partner',
            self::TYPE_RESOURCE_SPEAKERS => 'Resource Speakers & Mentors',
            self::TYPE_SPONSORSHIP       => 'Sponsorship & Grant Support',
            default                      => 'Joint Organizer & Co-Host',
        };
    }

    public function getPartnershipTypeBadgeColor(): string
    {
        return match ($this->partnership_type) {
            self::TYPE_TECHNICAL_SUPPORT => 'bg-cyan-50 text-cyan-800 border-cyan-200',
            self::TYPE_RESOURCE_SPEAKERS => 'bg-purple-50 text-purple-800 border-purple-200',
            self::TYPE_SPONSORSHIP       => 'bg-amber-50 text-amber-800 border-amber-200',
            default                      => 'bg-indigo-50 text-indigo-800 border-indigo-200',
        };
    }

    public function getDefaultLetterContent(): string
    {
        if (!empty($this->letter_content)) {
            return $this->letter_content;
        }

        $partnerName = $this->partner?->name ?? 'Partner Organization';
        $contactPerson = $this->partner?->contact_person ?? 'Representative';
        $title = $this->title;

        if ($this->partnership_type === self::TYPE_TECHNICAL_SUPPORT) {
            return "Dear {$contactPerson},\n\n"
                . "Warm greetings from the Computer Society (COMSOC) of the University of Makati!\n\n"
                . "This letter formally confirms COMSOC's acceptance and commitment to serve as the Official Technical Partner for \"{$title}\" organized by {$partnerName} for Academic Year {$this->academic_year}.\n\n"
                . "As the premier student technology organization of CCIS, COMSOC will deploy a dedicated Technical Operations Committee to provide:\n"
                . "1. Audio-Visual, Zoom Live Stream Broadcast, and Stage Technical Cueing Operations\n"
                . "2. Event Platform, Registration Management, and Technical System Support\n"
                . "3. On-Site Technical Facilitators and Logistics Crew\n\n"
                . "We look forward to collaborating closely with your team to deliver a seamless technical execution of the event.\n\n"
                . "Sincerely,\n"
                . "The Executive Board\n"
                . "Computer Society (COMSOC) - University of Makati";
        }

        return "Dear {$contactPerson},\n\n"
            . "Warm greetings from the Computer Society (COMSOC) of the University of Makati!\n\n"
            . "We are formally submitting this Letter of Intent and Activity Proposal for a joint partnership regarding \"{$title}\" for Academic Year {$this->academic_year}.\n\n"
            . "COMSOC is dedicated to empowering CCIS students with real-world technology skills, industry best practices, and collaborative learning opportunities. Through this partnership with {$partnerName}, we look forward to co-organizing impactful hands-on sessions, skill workshops, and collaborative knowledge-sharing events.\n\n"
            . "We look forward to your favorable endorsement and collaboration with our student body.\n\n"
            . "Sincerely,\n"
            . "The Executive Board\n"
            . "Computer Society (COMSOC) - University of Makati";
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PROPOSED     => 'Proposed',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_APPROVED     => 'Approved',
            self::STATUS_ACTIVE       => 'Active Partnership',
            self::STATUS_COMPLETED    => 'Completed',
            self::STATUS_ARCHIVED     => 'Archived',
            self::STATUS_DECLINED     => 'Declined',
            default                   => ucfirst($this->status),
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PROPOSED     => 'bg-gray-100 text-gray-700',
            self::STATUS_UNDER_REVIEW => 'bg-amber-100 text-amber-800',
            self::STATUS_APPROVED     => 'bg-blue-100 text-blue-800',
            self::STATUS_ACTIVE       => 'bg-emerald-100 text-emerald-800',
            self::STATUS_COMPLETED    => 'bg-purple-100 text-purple-800',
            self::STATUS_ARCHIVED     => 'bg-zinc-100 text-zinc-700',
            self::STATUS_DECLINED     => 'bg-red-100 text-red-800',
            default                   => 'bg-gray-100 text-gray-700',
        };
    }
}
