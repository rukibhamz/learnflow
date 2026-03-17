<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireEnrollments extends Command
{
    protected $signature = 'enrollments:expire';
    protected $description = 'Expire enrollments past their expires_at date';

    public function handle(): int
    {
        $total = 0;

        Enrollment::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->select('id')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$total) {
                $ids = $rows->pluck('id')->all();

                DB::transaction(function () use ($ids, &$total) {
                    Enrollment::query()
                        ->whereIn('id', $ids)
                        ->update(['completed_at' => null]);

                    $total += Enrollment::query()
                        ->whereIn('id', $ids)
                        ->delete();
                });
            });

        $this->info("Expired {$total} enrollments.");

        return self::SUCCESS;
    }
}

