<?php

namespace App\Models\Quize;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizeResponse extends Model
{
    protected $table = 'diagnostic_quiz_responses';

    protected $fillable = [
        'participant_first_name',
        'participant_last_name',
        'participant_email',
        'total_questions',
        'total_correct_answers',
        'score_percentage',
        'reward_coupon_code',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'total_questions' => 'integer',
            'total_correct_answers' => 'integer',
            'score_percentage' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }

    // This links one saved participant response to all selected answers.
    public function responseAnswers(): HasMany
    {
        return $this->hasMany(QuizeResponseAnswer::class, 'response_id');
    }
}
