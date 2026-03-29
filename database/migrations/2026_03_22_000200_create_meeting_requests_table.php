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
        // Business step: save each website meeting request in one simple table so the team can confirm and follow up quickly.
        if (! Schema::hasTable('meeting_requests')) {
            Schema::create('meeting_requests', function (Blueprint $table): void {
                $table->id();
                $table->string('full_name', 150);
                $table->string('email', 150);
                $table->string('phone', 20);
                $table->string('organization_name', 150)->nullable();
                $table->date('preferred_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('status', 30)->default('new');
                $table->timestamp('submitted_at')->useCurrent();
                $table->timestamps();
                $table->index(['preferred_date', 'status'], 'meeting_request_date_status_index');
                $table->index(['email', 'created_at'], 'meeting_request_email_created_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('meeting_requests')) {
            Schema::drop('meeting_requests');
        }
    }
};
