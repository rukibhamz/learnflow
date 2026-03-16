<?php

namespace App\Jobs;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateInvoicePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {
    }

    public function handle(): void
    {
        $this->order->loadMissing(['user', 'course']);

        $pdf = Pdf::loadView('invoices.course', [
            'order' => $this->order,
        ]);

        $path = 'invoices/order-' . $this->order->id . '.pdf';

        Storage::disk('local')->put($path, $pdf->output());

        $meta = $this->order->metadata ?? [];
        $meta['invoice_path'] = $path;
        $this->order->update(['metadata' => $meta]);
    }
}

