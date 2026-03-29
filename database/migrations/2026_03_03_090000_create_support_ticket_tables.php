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
        if (! Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table): void {
                $table->id();
                $table->string('ticket_number')->unique();
                $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('owner_company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('category', 60);
                $table->string('priority', 20);
                $table->text('description');
                $table->string('status', 30)->default('open');
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();
                $table->index(['owner_user_id', 'status'], 'support_ticket_owner_status_index');
                $table->index(['priority', 'status'], 'support_ticket_priority_status_index');
            });
        }

        if (! Schema::hasTable('support_ticket_comments')) {
            Schema::create('support_ticket_comments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
                $table->foreignId('commenter_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('comment');
                $table->timestamps();
                $table->index(['support_ticket_id', 'created_at'], 'support_ticket_comment_time_index');
            });
        }

        if (! Schema::hasTable('support_ticket_history')) {
            Schema::create('support_ticket_history', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
                $table->string('event_type', 40);
                $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('from_status', 30)->nullable();
                $table->string('to_status', 30)->nullable();
                $table->foreignId('support_ticket_comment_id')->nullable()->constrained('support_ticket_comments')->nullOnDelete();
                $table->text('message')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['support_ticket_id', 'created_at'], 'support_ticket_history_time_index');
            });
        }

        if (! Schema::hasTable('support_ticket_attachments')) {
            Schema::create('support_ticket_attachments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
                $table->foreignId('support_ticket_comment_id')->nullable()->constrained('support_ticket_comments')->nullOnDelete();
                $table->string('original_file_name');
                $table->string('stored_file_path');
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('mime_type', 120)->nullable();
                $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['support_ticket_id', 'support_ticket_comment_id'], 'support_ticket_attachment_scope_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('support_ticket_attachments')) {
            Schema::drop('support_ticket_attachments');
        }

        if (Schema::hasTable('support_ticket_history')) {
            Schema::drop('support_ticket_history');
        }

        if (Schema::hasTable('support_ticket_comments')) {
            Schema::drop('support_ticket_comments');
        }

        if (Schema::hasTable('support_tickets')) {
            Schema::drop('support_tickets');
        }
    }
};
