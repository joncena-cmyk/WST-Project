<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'event_id',
        'partnership_id',
        'uploaded_by',
        'file_path',
        'file_name',
        'file_size',
        'file_mime',
        'status',
        'notes',
        'version',
        'submitted_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size'    => 'integer',
            'version'      => 'integer',
            'submitted_at' => 'datetime',
            'approved_at'  => 'datetime',
        ];
    }

    // Status Constants
    const STATUS_DRAFT        = 'draft';
    const STATUS_SUBMITTED    = 'submitted';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_RETURNED     = 'returned';
    const STATUS_APPROVED     = 'approved';
    const STATUS_REJECTED     = 'rejected';

    // Category Constants
    const CATEGORY_PROPOSAL    = 'event_proposal';
    const CATEGORY_RESERVATION = 'reservation_letter';
    const CATEGORY_BUDGET      = 'budget_request';
    const CATEGORY_INTENT      = 'letter_of_intent';
    const CATEGORY_REPORT      = 'event_report';
    const CATEGORY_OTHER       = 'other';

    public static function categories(): array
    {
        return [
            self::CATEGORY_PROPOSAL    => 'Event Activity Proposal',
            self::CATEGORY_RESERVATION => 'Facility & Venue Reservation Letter',
            self::CATEGORY_BUDGET      => 'Budget Request & Financial Form',
            self::CATEGORY_INTENT      => 'Letter of Intent / Partnership',
            self::CATEGORY_REPORT      => 'Post-Event Accomplishment Report',
            self::CATEGORY_OTHER       => 'Other Official Document',
        ];
    }

    // Relationships
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function partnership(): BelongsTo
    {
        return $this->belongsTo(Partnership::class, 'partnership_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DocumentReview::class, 'document_id')->latest();
    }

    public function latestReview()
    {
        return $this->hasOne(DocumentReview::class, 'document_id')->latestOfMany();
    }

    // Helpers
    public function getCategoryLabel(): string
    {
        return self::categories()[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT        => 'Draft',
            self::STATUS_SUBMITTED    => 'Submitted',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_RETURNED     => 'Returned for Revision',
            self::STATUS_APPROVED     => 'Approved',
            self::STATUS_REJECTED     => 'Rejected',
            default                   => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT        => 'bg-gray-100 text-gray-700 border border-gray-200',
            self::STATUS_SUBMITTED    => 'bg-blue-50 text-blue-700 border border-blue-200',
            self::STATUS_UNDER_REVIEW => 'bg-amber-50 text-amber-700 border border-amber-200',
            self::STATUS_RETURNED     => 'bg-orange-50 text-orange-700 border border-orange-200',
            self::STATUS_APPROVED     => 'bg-green-50 text-green-700 border border-green-200',
            self::STATUS_REJECTED     => 'bg-red-50 text-red-700 border border-red-200',
            default                   => 'bg-gray-100 text-gray-700 border border-gray-200',
        };
    }

    public function getFormattedFileSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_RETURNED]);
    }

    public function isSubmittable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_RETURNED]);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
