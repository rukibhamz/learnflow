<?php

namespace App\Jobs;

use App\Mail\PaymentReceipt;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaymentReceiptEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {
    }

    public function handle(): void
    {
        $this->order->loadMissing(['user', 'course']);

        Mail::to($this->order->user->email)
            ->send(new PaymentReceipt($this->order));
    }
}

