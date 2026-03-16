<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Course::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:courses,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'language' => ['nullable', 'string', 'max:10'],
            'category' => ['nullable', 'string', 'max:100'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['string', 'max:255'],
            'outcomes' => ['nullable', 'array'],
            'outcomes.*' => ['string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
