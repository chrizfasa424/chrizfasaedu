<?php

namespace App\Mail;

use App\Models\Admission;
use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionReceived extends Mailable
{
    use Queueable, SerializesModels;

    public Admission $admission;
    public ?School $school;

    public function __construct(Admission $admission, ?School $school = null)
    {
        $this->admission = $admission;
        $this->school = $school ?? $admission->school;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Received - ' . ($this->school?->name ?? 'Our School'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-received',
        );
    }
}
