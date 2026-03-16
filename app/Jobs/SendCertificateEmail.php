<?php

namespace App\Jobs;

use App\Mail\CertificateIssued;
use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCertificateEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $certificateId
    ) {
    }

    public function handle(): void
    {
        $certificate = Certificate::with(['user', 'course'])->findOrFail($this->certificateId);

        Mail::to($certificate->user->email)
            ->send(new CertificateIssued($certificate));
    }
}

