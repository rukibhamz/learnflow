<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateIssued extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Certificate $certificate
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your certificate is ready – ' . $this->certificate->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.certificate-issued',
        );
    }
}

