<?php

namespace App\Models\Quize;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizeQuestion extends Model
{
    protected $table = 'diagnostic_quiz_questions';

    protected $fillable = [
        'phase_title',
        'question_text',
        'question_support_details',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'question_support_details' => 'array',
            'display_order' => 'integer',
        ];
    }

    // This links one quiz question to the answer choices shown on the page.
    public function answerOptions(): HasMany
    {
        return $this->hasMany(QuizeAnswerOption::class, 'question_id');
    }

    // This links one quiz question to all saved participant answers.
    public function responseAnswers(): HasMany
    {
        return $this->hasMany(QuizeResponseAnswer::class, 'question_id');
    }
}
