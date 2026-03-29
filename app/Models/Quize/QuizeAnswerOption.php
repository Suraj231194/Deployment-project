<?php

namespace App\Models\Quize;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizeAnswerOption extends Model
{
    protected $table = 'diagnostic_quiz_answer_options';

    protected $fillable = [
        'question_id',
        'option_label',
        'option_text',
        'is_correct_answer',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'question_id' => 'integer',
            'is_correct_answer' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    // This links one answer option back to the question it belongs to.
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizeQuestion::class, 'question_id');
    }
}
