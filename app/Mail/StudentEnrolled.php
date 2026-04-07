<?php

namespace App\Mail;

use App\Models\Admission;
use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentEnrolled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Admission $admission,
        public ?School   $school,
        public string    $loginEmail,
        public string    $plainPassword,
        public string    $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrolment Confirmed — ' . ($this->school?->name ?? 'Our School'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-enrolled',
        );
    }
}
