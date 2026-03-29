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
        // Business step: keep enquiry types in one master table so the contact form options stay business-managed.
        if (! Schema::hasTable('enquiry_types')) {
            Schema::create('enquiry_types', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 80)->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Business step: save every website enquiry in one simple table for follow-up and future reporting.
        if (! Schema::hasTable('contact_us_enquiries')) {
            Schema::create('contact_us_enquiries', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('enquiry_type_id')->constrained('enquiry_types')->restrictOnDelete();
                $table->string('full_name', 150);
                $table->string('email', 150);
                $table->string('phone', 20);
                $table->text('message');
                $table->string('status', 30)->default('new');
                $table->timestamp('submitted_at')->useCurrent();
                $table->timestamps();
                $table->index(['enquiry_type_id', 'status'], 'contact_us_enquiry_type_status_index');
                $table->index(['email', 'created_at'], 'contact_us_enquiry_email_created_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('contact_us_enquiries')) {
            Schema::drop('contact_us_enquiries');
        }

        if (Schema::hasTable('enquiry_types')) {
            Schema::drop('enquiry_types');
        }
    }
};
