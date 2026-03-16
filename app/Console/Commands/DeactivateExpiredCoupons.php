<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class DeactivateExpiredCoupons extends Command
{
    protected $signature = 'coupons:deactivate-expired';
    protected $description = 'Deactivate expired coupons';

    public function handle(): int
    {
        $count = Coupon::where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['is_active' => false]);

        $this->info("Deactivated {$count} expired coupons.");

        return self::SUCCESS;
    }
}

