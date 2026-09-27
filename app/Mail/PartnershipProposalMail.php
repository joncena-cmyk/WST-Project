<?php

namespace App\Mail;

use App\Models\Partnership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnershipProposalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Partnership $partnership,
        public ?string $customMessage = null
    ) {}

    public function envelope(): Envelope
    {
        $ref = 'COL-' . $this->partnership->academic_year . '-' . str_pad((string)$this->partnership->id, 4, '0', STR_PAD_LEFT);

        return new Envelope(
            subject: "Official Partnership Letter of Intent & Proposal [{$ref}] - COMSOC x {$this->partnership->partner->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partnership-proposal',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
