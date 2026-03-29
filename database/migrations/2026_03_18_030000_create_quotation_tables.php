<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->string('requester_type', 20)->default('guest');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('owner_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('target_type', 20)->default('self');
            $table->string('target_name')->nullable();
            $table->string('target_email')->nullable();
            $table->string('target_phone')->nullable();
            $table->foreignId('target_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('status', 30)->default('generated');
            $table->string('currency', 3)->default('INR');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('price_after_gst', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('guest_session_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['owner_user_id', 'owner_company_id'], 'quotation_owner_scope_index');
            $table->index(['target_company_id', 'status'], 'quotation_target_scope_index');
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->string('product_name');
            $table->string('sku');
            $table->string('variant_name')->nullable();
            $table->string('price_type', 30)->nullable();
            $table->string('currency', 3)->default('INR');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('gst_rate', 5, 2)->default(0);
            $table->decimal('unit_tax_amount', 12, 2)->default(0);
            $table->decimal('unit_price_after_gst', 12, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('unit_discount_amount', 12, 2)->default(0);
            $table->decimal('line_subtotal', 12, 2)->default(0);
            $table->decimal('line_tax_amount', 12, 2)->default(0);
            $table->decimal('line_price_after_gst', 12, 2)->default(0);
            $table->decimal('line_discount_amount', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
