<?php

namespace App\Livewire;

use App\Models\SearchLog;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminSearchAnalytics extends Component
{
    public string $period = '30';

    public function render()
    {
        $days = (int) $this->period;

        $popular = SearchLog::where('created_at', '>=', now()->subDays($days))
            ->select('term', DB::raw('COUNT(*) as count'), DB::raw('ROUND(AVG(results_count)) as avg_results'))
            ->groupBy('term')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        $totalSearches = SearchLog::where('created_at', '>=', now()->subDays($days))->count();
        $uniqueTerms = SearchLog::where('created_at', '>=', now()->subDays($days))->distinct('term')->count('term');
        $zeroResults = SearchLog::where('created_at', '>=', now()->subDays($days))
            ->where('results_count', 0)->count();

        $dailyVolume = SearchLog::where('created_at', '>=', now()->subDays(min($days, 30)))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        return view('livewire.admin-search-analytics', [
            'popular' => $popular,
            'totalSearches' => $totalSearches,
            'uniqueTerms' => $uniqueTerms,
            'zeroResults' => $zeroResults,
            'dailyVolume' => $dailyVolume,
        ]);
    }
}
