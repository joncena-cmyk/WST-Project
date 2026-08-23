<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'reviewed_by',
        'action',
        'remarks',
    ];

    const ACTION_APPROVED = 'approved';
    const ACTION_RETURNED = 'returned';
    const ACTION_REJECTED = 'rejected';

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getActionLabel(): string
    {
        return match ($this->action) {
            self::ACTION_APPROVED => 'Approved',
            self::ACTION_RETURNED => 'Returned for Revision',
            self::ACTION_REJECTED => 'Rejected',
            default               => ucfirst($this->action),
        };
    }

    public function getActionColor(): string
    {
        return match ($this->action) {
            self::ACTION_APPROVED => 'bg-green-100 text-green-800 border border-green-200',
            self::ACTION_RETURNED => 'bg-orange-100 text-orange-800 border border-orange-200',
            self::ACTION_REJECTED => 'bg-red-100 text-red-800 border border-red-200',
            default               => 'bg-gray-100 text-gray-800',
        };
    }
}
