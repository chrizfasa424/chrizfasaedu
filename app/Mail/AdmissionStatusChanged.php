<?php

namespace App\Mail;

use App\Models\Admission;
use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Admission $admission,
        public ?School $school = null,
    ) {}

    public function envelope(): Envelope
    {
        $status  = $this->admission->status->value;
        $subject = match ($status) {
            'approved' => 'Application Approved — ' . ($this->school?->name ?? 'Our School'),
            'rejected' => 'Application Update — ' . ($this->school?->name ?? 'Our School'),
            default    => 'Application Status Update — ' . ($this->school?->name ?? 'Our School'),
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-status-changed',
        );
    }
}
