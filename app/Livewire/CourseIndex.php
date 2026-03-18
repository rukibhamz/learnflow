<?php

namespace App\Livewire;

use App\Enums\CourseLevel;
use App\Models\Course;
use App\Models\SearchLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CourseIndex extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: [])]
    public array $levels = [];

    #[Url(except: '')]
    public string $priceFilter = '';

    #[Url(except: '')]
    public string $language = '';

    #[Url(except: '')]
    public string $categoryFilter = '';

    #[Url(except: 'newest')]
    public string $sort = 'newest';

    public array $highlights = [];
    public int $scoutTotal = 0;
    public array $suggestions = [];
    public bool $showSuggestions = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->fetchSuggestions();
    }

    public function updatingSearch(): void      { $this->resetPage(); }
    public function updatingLevels(): void      { $this->resetPage(); }
    public function updatingPriceFilter(): void { $this->resetPage(); }
    public function updatingLanguage(): void    { $this->resetPage(); }
    public function updatingCategoryFilter(): void { $this->resetPage(); }
    public function updatingSort(): void        { $this->resetPage(); }

    public function fetchSuggestions(): void
    {
        $term = trim($this->search);
        if (strlen($term) < 2) {
            $this->suggestions = [];
            $this->showSuggestions = false;
            return;
        }

        $this->suggestions = Course::published()
            ->where('title', 'like', "{$term}%")
            ->orderBy('enrolled_count', 'desc')
            ->limit(5)
            ->pluck('title')
            ->toArray();

        $this->showSuggestions = count($this->suggestions) > 0;
    }

    public function selectSuggestion(string $title): void
    {
        $this->search = $title;
        $this->showSuggestions = false;
        $this->resetPage();
    }

    public function dismissSuggestions(): void
    {
        $this->showSuggestions = false;
    }

    public function toggleLevel(string $level): void
    {
        $this->levels = in_array($level, $this->levels)
            ? array_values(array_diff($this->levels, [$level]))
            : [...$this->levels, $level];
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'levels', 'priceFilter', 'language', 'categoryFilter', 'sort']);
        $this->highlights = [];
        $this->resetPage();
    }

    public function render()
    {
        $this->highlights = [];

        $courses = trim($this->search) !== ''
            ? $this->scoutSearch()
            : $this->eloquentSearch();

        if (trim($this->search) !== '') {
            SearchLog::log($this->search, $this->scoutTotal, auth()->id());
        }

        $languages = collect(Course::cachedLanguages());
        $categories = collect(Course::cachedCategories());

        return view('livewire.course-index', [
            'courses'      => $courses,
            'languages'    => $languages,
            'categories'   => $categories,
            'levelOptions' => CourseLevel::cases(),
        ]);
    }

    private function scoutSearch(): LengthAwarePaginator
    {
        $filters = [];

        if (! empty($this->levels)) {
            $list = implode(', ', array_map(fn($l) => '"' . $l . '"', $this->levels));
            $filters[] = "level IN [{$list}]";
        }

        if ($this->priceFilter === 'free') {
            $filters[] = 'price = 0';
        } elseif ($this->priceFilter === 'paid') {
            $filters[] = 'price > 0';
        }

        if ($this->language) {
            $filters[] = 'language = "' . $this->language . '"';
        }

        if ($this->categoryFilter) {
            $filters[] = 'category = "' . $this->categoryFilter . '"';
        }

        $sortMap = [
            'popular'    => 'enrolled_count:desc',
            'rated'      => 'average_rating:desc',
            'price_low'  => 'price:asc',
            'price_high' => 'price:desc',
            'newest'     => 'created_at:desc',
        ];

        try {
            $raw = Course::search($this->search)
                ->options([
                    'filter'                => implode(' AND ', $filters) ?: null,
                    'sort'                  => [$sortMap[$this->sort] ?? 'created_at:desc'],
                    'attributesToHighlight' => ['title', 'short_description', 'instructor_name'],
                    'highlightPreTag'       => '<mark class="bg-primary/20 text-primary rounded px-0.5">',
                    'highlightPostTag'      => '</mark>',
                ])
                ->raw();

            $this->scoutTotal = $raw['totalHits'] ?? $raw['estimatedTotalHits'] ?? 0;

            foreach ($raw['hits'] ?? [] as $hit) {
                if (isset($hit['_formatted'])) {
                    $this->highlights[$hit['id']] = $hit['_formatted'];
                }
            }

            $ids = collect($raw['hits'] ?? [])->pluck('id');

            if ($ids->isEmpty()) {
                return new LengthAwarePaginator([], 0, 12, $this->getPage());
            }

            // Preserve Meilisearch relevance order via FIELD() on MySQL
            $idList = $ids->implode(',');

            return Course::published()
                ->with(['instructor', 'media'])
                ->withCount(['enrollments', 'reviews' => fn($q) => $q->whereNotNull('approved_at')])
                ->withAvg(['reviews' => fn($q) => $q->whereNotNull('approved_at')], 'rating')
                ->whereIn('id', $ids)
                ->orderByRaw("FIELD(id, {$idList})")
                ->paginate(12);

        } catch (\Throwable) {
            return $this->eloquentSearch(fallbackSearch: $this->search);
        }
    }

    private function eloquentSearch(string $fallbackSearch = ''): LengthAwarePaginator
    {
        $query = Course::query()
            ->published()
            ->with(['instructor', 'media'])
            ->withCount(['enrollments', 'reviews' => fn($q) => $q->whereNotNull('approved_at')])
            ->withAvg(['reviews' => fn($q) => $q->whereNotNull('approved_at')], 'rating');

        if ($fallbackSearch !== '') {
            $query->where(function ($q) use ($fallbackSearch) {
                $q->where('title', 'like', "%{$fallbackSearch}%")
                  ->orWhere('short_description', 'like', "%{$fallbackSearch}%");
            });
        }

        if (! empty($this->levels)) {
            $query->whereIn('level', $this->levels);
        }

        if ($this->priceFilter === 'free') {
            $query->where('price', 0);
        } elseif ($this->priceFilter === 'paid') {
            $query->where('price', '>', 0);
        }

        if ($this->language) {
            $query->where('language', $this->language);
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        match ($this->sort) {
            'popular'    => $query->orderByDesc('enrollments_count'),
            'rated'      => $query->orderByDesc('reviews_avg_rating'),
            'price_low'  => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default      => $query->latest(),
        };

        $paginator = $query->paginate(12);
        $this->scoutTotal = $paginator->total();

        return $paginator;
    }
}