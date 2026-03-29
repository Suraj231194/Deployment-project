<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('support_ticket_categories')) {
            Schema::create('support_ticket_categories', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 60)->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('support_ticket_categories')) {
            Schema::drop('support_ticket_categories');
        }
    }
};
