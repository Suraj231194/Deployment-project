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
        // Business step: keep all public FAQ content in one simple table so business users can manage display order centrally.
        if (! Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table): void {
                $table->id();
                $table->string('category', 100);
                $table->string('question', 255);
                $table->text('answer');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_default_open')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }


    public function down(): void
    {
        if (Schema::hasTable('faqs')) {
            Schema::drop('faqs');
        }
    }
};
