<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = [
        'instructor_id',
        'amount',
        'platform_fee',
        'status',
        'method',
        'stripe_transfer_id',
        'notes',
        'period_start',
        'period_end',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'platform_fee' => 'integer',
            'period_start' => 'date',
            'period_end' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function markPaid(?string $stripeTransferId = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'stripe_transfer_id' => $stripeTransferId,
        ]);
    }

    public function formattedAmount(): string
    {
        return currency_symbol() . number_format($this->amount / 100, 2);
    }

    public function formattedFee(): string
    {
        return currency_symbol() . number_format($this->platform_fee / 100, 2);
    }
}
