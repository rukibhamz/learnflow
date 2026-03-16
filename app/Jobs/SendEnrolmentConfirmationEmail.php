<?php

namespace App\Jobs;

use App\Mail\EnrolmentConfirmation;
use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEnrolmentConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Enrollment $enrollment
    ) {}

    public function handle(): void
    {
        $this->enrollment->loadMissing(['user', 'course']);

        Mail::to($this->enrollment->user->email)
            ->send(new EnrolmentConfirmation($this->enrollment));
    }
}
