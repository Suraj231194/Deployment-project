<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Business step: store each quiz question in one small master table so the backend owns the quiz content.
        if (! Schema::hasTable('diagnostic_quiz_questions')) {
            Schema::create('diagnostic_quiz_questions', function (Blueprint $table): void {
                $table->id();
                $table->string('phase_title', 150);
                $table->text('question_text');
                $table->json('question_support_details')->nullable();
                $table->unsignedTinyInteger('display_order')->default(1);
                $table->timestamps();
            });
        }

        // Business step: store the answer choices for each question and mark which choice is correct for scoring.
        if (! Schema::hasTable('diagnostic_quiz_answer_options')) {
            Schema::create('diagnostic_quiz_answer_options', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('question_id')->constrained('diagnostic_quiz_questions')->cascadeOnDelete();
                $table->string('option_label', 5);
                $table->string('option_text', 255);
                $table->boolean('is_correct_answer')->default(false);
                $table->unsignedTinyInteger('display_order')->default(1);
                $table->timestamps();

                $table->unique(['question_id', 'option_label'], 'diagnostic_quiz_option_label_unique');
            });
        }

        // Business step: store the participant details and final score for each completed quiz response.
        if (! Schema::hasTable('diagnostic_quiz_responses')) {
            Schema::create('diagnostic_quiz_responses', function (Blueprint $table): void {
                $table->id();
                $table->string('participant_first_name', 100);
                $table->string('participant_last_name', 100)->nullable();
                $table->string('participant_email', 150);
                $table->unsignedTinyInteger('total_correct_answers')->default(0);
                $table->unsignedTinyInteger('score_percentage')->default(0);
                $table->string('reward_coupon_code', 50)->nullable();
                $table->timestamp('submitted_at')->useCurrent();
                $table->timestamps();

            });
        }

        // Business step: store the selected answer for each question inside one participant response.
        if (! Schema::hasTable('diagnostic_quiz_response_answers')) {
            Schema::create('diagnostic_quiz_response_answers', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('response_id')->constrained('diagnostic_quiz_responses')->cascadeOnDelete();
                $table->foreignId('question_id')->constrained('diagnostic_quiz_questions')->restrictOnDelete();
                $table->foreignId('selected_option_id')->constrained('diagnostic_quiz_answer_options')->restrictOnDelete();
                $table->boolean('is_correct_answer')->default(false);
                $table->timestamps();

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('diagnostic_quiz_response_answers')) {
            Schema::drop('diagnostic_quiz_response_answers');
        }

        if (Schema::hasTable('diagnostic_quiz_responses')) {
            Schema::drop('diagnostic_quiz_responses');
        }

        if (Schema::hasTable('diagnostic_quiz_answer_options')) {
            Schema::drop('diagnostic_quiz_answer_options');
        }

        if (Schema::hasTable('diagnostic_quiz_questions')) {
            Schema::drop('diagnostic_quiz_questions');
        }
    }
};
