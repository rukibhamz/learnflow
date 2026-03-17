<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class SearchLog extends Model
{
    protected $fillable = ['term', 'results_count', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $term, int $resultsCount, ?int $userId = null): void
    {
        static::create([
            'term' => strtolower(trim($term)),
            'results_count' => $resultsCount,
            'user_id' => $userId,
        ]);
    }

    public static function popularTerms(int $limit = 10, int $days = 30): \Illuminate\Support\Collection
    {
        return static::where('created_at', '>=', now()->subDays($days))
            ->select('term', DB::raw('COUNT(*) as search_count'))
            ->groupBy('term')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->pluck('search_count', 'term');
    }
}
