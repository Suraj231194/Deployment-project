<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('application')->nullable();
                $table->string('slug')->unique();
                $table->boolean('IsDisplayedOnHomePage')->default(false);
                $table->string('default_image_path')->nullable();
                $table->decimal('gst_rate', 5, 2)->default(18.00);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('subcategories')) {
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->string('default_image_path')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['category_id', 'slug'], 'subcategory_category_slug_unique');
                $table->index(['category_id', 'sort_order'], 'subcategory_category_sort_index');
            });
        }

         if (! Schema::hasTable('product_specifications')) {
            Schema::create('product_specifications', function (Blueprint $table) {
                $table->id();
                $table->json('specs')->nullable();
                $table->timestamps();
            }); 
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->nullOnDelete();
                $table->foreignId('product_specifications_id')->nullable()->constrained('product_specifications')->nullOnDelete();
                $table->string('slug')->unique();    
                $table->string('base_sku')->nullable();
                $table->boolean('is_published')->default(false);
                $table->unsignedBigInteger('product_image_id')->nullable();
                $table->string('sku')->unique();
                $table->string('name');
                $table->string('brand')->nullable();
                $table->text('description')->nullable();
                // Business badges keep catalog and detail merchandising labels data-driven.
                $table->string('badges')->nullable();
                // Business overview keeps the product detail page content data-driven instead of hardcoded.
                $table->text('product_overview')->nullable();
                $table->decimal('gst_rate', 5, 2)->nullable();
                $table->string('visibility_scope', 20)->default('public');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('product_prices')) {
            Schema::create('product_prices', function (Blueprint $table) {
                $table->id();
                // Pricing is linked to variants and can hold both default and company_price rows.
                $table->unsignedBigInteger('product_variant_id')->nullable();
                $table->string('price_type', 30);
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->decimal('amount', 12, 2);
                $table->string('DiscountType', 20)->default('cash');
                $table->decimal('Discount', 12, 2)->default(0);
                $table->decimal('gst_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('price_after_gst', 12, 2)->default(0);
                $table->string('currency', 3)->default('INR');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['product_variant_id', 'price_type'], 'product_prices_variant_price_type_index');
            });
        }

       
       

        if (! Schema::hasTable('proforma_invoices')) {
            Schema::create('proforma_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('pi_number')->unique();
                $table->string('requester_type', 20)->default('guest');
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('owner_company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('target_type', 20)->default('self');
                $table->string('target_name')->nullable();
                $table->string('target_email')->nullable();
                $table->string('target_phone')->nullable();
                $table->foreignId('target_company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('status', 30)->default('draft');
                $table->string('currency', 3)->default('INR');
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('price_after_gst', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('guest_session_id')->nullable()->index();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['owner_user_id', 'owner_company_id'], 'pi_owner_scope_index');
                $table->index(['target_company_id', 'status'], 'pi_target_scope_index');
            });
        }

        if (! Schema::hasTable('proforma_invoice_items')) {
            Schema::create('proforma_invoice_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proforma_invoice_id')->constrained('proforma_invoices')->cascadeOnDelete();
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

        if (! Schema::hasTable('user_activity_logs')) {
            Schema::create('user_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->string('session_id')->index();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('user_type', 30)->default('guest');
                $table->string('user_name')->nullable();
                $table->string('user_email')->nullable();
                $table->string('activity_type', 40);
                $table->string('path')->nullable();
                $table->json('payload')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('proforma_invoice_items');
        Schema::dropIfExists('proforma_invoices');
        Schema::dropIfExists('product_specifications'); // ✅ ADDED
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('products');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
    }
};
