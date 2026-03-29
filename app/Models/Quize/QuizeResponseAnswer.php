<?php

namespace App\Models\Quize;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizeResponseAnswer extends Model
{
    protected $table = 'diagnostic_quiz_response_answers';

    protected $fillable = [
        'response_id',
        'question_id',
        'selected_option_id',
        'is_correct_answer',
    ];

    protected function casts(): array
    {
        return [
            'response_id' => 'integer',
            'question_id' => 'integer',
            'selected_option_id' => 'integer',
            'is_correct_answer' => 'boolean',
        ];
    }

    // This links one selected answer row back to the parent participant response.
    public function response(): BelongsTo
    {
        return $this->belongsTo(QuizeResponse::class, 'response_id');
    }

    // This links the saved row back to the quiz question it answers.
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizeQuestion::class, 'question_id');
    }

    // This links the saved row back to the answer option chosen by the participant.
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuizeAnswerOption::class, 'selected_option_id');
    }
}
