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
        if (Schema::hasTable('product_variants')) {
            return;
        }

        Schema::create('product_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('variant_name')->default('Default Variant');
            // Business technical specifications are stored per visible variant for the product detail page.
            $table->json('technical_specification_json')->nullable();
            $table->unsignedInteger('min_order_quantity')->default(1);
            $table->unsignedInteger('max_order_quantity')->nullable();
            // Business B2C quantity limits let retail buyers follow smaller order rules on the same sellable variant.
            $table->unsignedInteger('b2c_min_order_quantity')->nullable();
            $table->unsignedInteger('b2c_max_order_quantity')->nullable();
            // Business B2B quantity limits let trade buyers follow larger quantity rules on the same sellable variant.
            $table->unsignedInteger('b2b_min_order_quantity')->nullable();
            $table->unsignedInteger('b2b_max_order_quantity')->nullable();
            $table->unsignedInteger('lot_size')->default(1);
            $table->string('model_number')->nullable();
            $table->string('catalog_number')->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'is_active'], 'product_variant_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_variants')) {
            Schema::drop('product_variants');
        }
    }
};
