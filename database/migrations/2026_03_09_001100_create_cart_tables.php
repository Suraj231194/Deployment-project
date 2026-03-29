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
        // Step 1: create one cart row per logged-in shopper or guest session.
        Schema::create('carts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->char('currency', 3)->default('INR');
            $table->timestamps();

            // Step 2: keep one active cart per account and one active cart per guest session.
            $table->unique('user_id', 'carts_user_id_unique');
            $table->unique('session_id', 'carts_session_id_unique');
            $table->index('session_id', 'carts_session_id_index');
        });

        // Step 3: create minimal cart item rows using only the selected variant and quantity.
        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            // Step 4: prevent duplicate rows for the same variant inside one cart.
            $table->unique(['cart_id', 'product_variant_id'], 'cart_items_cart_variant_unique');
            $table->index('cart_id', 'cart_items_cart_id_index');
            $table->index('product_variant_id', 'cart_items_variant_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: drop cart items before carts because of the foreign key.
        Schema::dropIfExists('cart_items');

        // Step 2: drop cart headers.
        Schema::dropIfExists('carts');
    }
};
