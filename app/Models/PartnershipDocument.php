<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PartnershipDocument extends Model
{
    use HasFactory;

    const TYPE_MOA_MOU           = 'moa_mou';
    const TYPE_LETTER_OF_INTENT  = 'letter_of_intent';
    const TYPE_SPONSORSHIP_DECK  = 'sponsorship_deck';
    const TYPE_INVITATION        = 'invitation';
    const TYPE_COMPLETION_REPORT = 'completion_report';
    const TYPE_OTHER             = 'other';

    protected $fillable = [
        'partnership_id',
        'title',
        'document_type',
        'file_path',
        'file_name',
        'file_size',
        'uploaded_by',
    ];

    public function partnership(): BelongsTo
    {
        return $this->belongsTo(Partnership::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTypeLabel(): string
    {
        return match ($this->document_type) {
            self::TYPE_MOA_MOU           => 'MoA / MoU Agreement',
            self::TYPE_LETTER_OF_INTENT  => 'Letter of Intent',
            self::TYPE_SPONSORSHIP_DECK  => 'Sponsorship Package',
            self::TYPE_INVITATION        => 'Partnership Invitation',
            self::TYPE_COMPLETION_REPORT => 'Activity Completion Report',
            default                      => 'Supporting Document',
        };
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 1) . ' KB';
    }
}
