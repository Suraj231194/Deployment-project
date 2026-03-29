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
        if (! Schema::hasTable('diagnostic_quiz_responses')) {
            return;
        }

        if (! Schema::hasColumn('diagnostic_quiz_responses', 'total_questions')) {
            Schema::table('diagnostic_quiz_responses', function (Blueprint $table): void {
                $table->unsignedTinyInteger('total_questions')->default(0)->after('participant_email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('diagnostic_quiz_responses')) {
            return;
        }

        if (Schema::hasColumn('diagnostic_quiz_responses', 'total_questions')) {
            Schema::table('diagnostic_quiz_responses', function (Blueprint $table): void {
                $table->dropColumn('total_questions');
            });
        }
    }
};
