<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
        'features',
        'course_limit',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'price_monthly' => 'integer',
            'price_yearly' => 'integer',
            'course_limit' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function formattedMonthlyPrice(): string
    {
        return '$' . number_format($this->price_monthly / 100, 2);
    }

    public function formattedYearlyPrice(): string
    {
        return $this->price_yearly
            ? '$' . number_format($this->price_yearly / 100, 2)
            : '';
    }

    public function yearlyMonthlySavings(): ?int
    {
        if (!$this->price_yearly) {
            return null;
        }
        $yearlyMonthly = $this->price_yearly / 12;
        return (int) round((1 - ($yearlyMonthly / $this->price_monthly)) * 100);
    }
}
