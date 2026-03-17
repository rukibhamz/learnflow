<?php

namespace App\Models;

use App\Enums\QuizQuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankQuestion extends Model
{
    use HasFactory;

    protected $table = 'question_bank';

    protected $fillable = [
        'course_id',
        'question',
        'type',
        'options',
        'correct_answer',
        'explanation',
        'category',
        'points',
    ];

    protected function casts(): array
    {
        return [
            'type' => QuizQuestionType::class,
            'options' => 'array',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
