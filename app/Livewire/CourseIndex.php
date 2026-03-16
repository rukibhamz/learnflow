<?php

namespace App\Livewire;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class CourseIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $levels = [];
    public $priceFilter = '';
    public $language = '';
    public $sort = 'newest';

    protected $queryString = [
        'search' => ['except' => ''],
        'levels' => ['except' => []],
        'priceFilter' => ['except' => ''],
        'language' => ['except' => ''],
        'sort' => ['except' => 'newest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLevels()
    {
        $this->resetPage();
    }

    public function updatingPriceFilter()
    {
        $this->resetPage();
    }

    public function updatingLanguage()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function toggleLevel($level)
    {
        if (in_array($level, $this->levels)) {
            $this->levels = array_values(array_diff($this->levels, [$level]));
        } else {
            $this->levels[] = $level;
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'levels', 'priceFilter', 'language', 'sort']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Course::query()
            ->published()
            ->with(['instructor', 'media'])
            ->withCount(['enrollments', 'reviews' => fn($q) => $q->whereNotNull('approved_at')])
            ->withAvg(['reviews' => fn($q) => $q->whereNotNull('approved_at')], 'rating');

        if ($this->search) {
            $searchIds = Course::search($this->search)->keys();
            if ($searchIds->isNotEmpty()) {
                $query->whereIn('id', $searchIds);
            } else {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('short_description', 'like', '%' . $this->search . '%');
            }
        }

        if (!empty($this->levels)) {
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

        switch ($this->sort) {
            case 'popular':
                $query->orderByDesc('enrollments_count');
                break;
            case 'rated':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderByDesc('price');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $courses = $query->paginate(12);

        $languages = Course::published()
            ->whereNotNull('language')
            ->distinct()
            ->pluck('language')
            ->filter();

        return view('livewire.course-index', [
            'courses' => $courses,
            'languages' => $languages,
            'levelOptions' => CourseLevel::cases(),
        ]);
    }
}
