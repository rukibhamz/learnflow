<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrolmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Enrollment $enrollment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . $this->enrollment->course->title . ' - LearnFlow',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enrolment-confirmation',
        );
    }
}
