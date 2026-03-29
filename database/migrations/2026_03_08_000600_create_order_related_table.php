<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('placed_by_user_id')->constrained('users');
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('status', 32)->default('draft');
                $table->char('currency', 3)->default('INR');
                $table->decimal('subtotal_amount', 19, 4)->default(0);
                $table->decimal('tax_amount', 19, 4)->default(0);
                $table->decimal('discount_amount', 19, 4)->default(0);
                $table->decimal('shipping_amount', 19, 4)->default(0);
                $table->decimal('adjustment_amount', 19, 4)->default(0);
                $table->decimal('rounding_amount', 19, 4)->default(0);
                $table->decimal('total_amount', 19, 4)->default(0);
                $table->json('pricing_snapshot')->nullable();
                $table->text('notes')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('cancelled_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['placed_by_user_id', 'status'], 'orders_user_status_index');
                $table->index(['company_id', 'status'], 'orders_company_status_index');
                $table->index(['status', 'created_at'], 'orders_status_created_index');
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
                $table->string('sku', 100)->nullable();
                $table->string('product_name');
                $table->string('variant_name')->nullable();
                $table->text('description')->nullable();
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('unit_price', 19, 4)->default(0);
                $table->decimal('subtotal_amount', 19, 4)->default(0);
                $table->decimal('discount_amount', 19, 4)->default(0);
                $table->decimal('tax_amount', 19, 4)->default(0);
                $table->decimal('total_amount', 19, 4)->default(0);
                $table->unsignedInteger('sort_order')->default(0);
                $table->json('item_snapshot')->nullable();
                $table->timestamps();
                $table->index('order_id', 'order_items_order_index');
                $table->index('product_id', 'order_items_product_index');
                $table->index('product_variant_id', 'order_items_variant_index');
                $table->index(['order_id', 'sort_order'], 'order_items_order_sort_index');
            });
        }

        if (! Schema::hasTable('order_addresses')) {
            Schema::create('order_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->string('address_type', 16); // billing, shipping
                $table->string('contact_name')->nullable();
                $table->string('company_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone', 32)->nullable();
                $table->string('gstin', 20)->nullable();
                $table->string('line1');
                $table->string('line2')->nullable();
                $table->string('landmark')->nullable();
                $table->string('city', 128);
                $table->string('state', 128);
                $table->string('postal_code', 20);
                $table->char('country_code', 2)->default('IN');
                $table->timestamps();
                $table->unique(['order_id', 'address_type'], 'order_addresses_order_type_unique');
                $table->index('order_id', 'order_addresses_order_index');
            });
        }

        if (! Schema::hasTable('order_tax_lines')) {
            Schema::create('order_tax_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('order_item_id')->nullable()->constrained('order_items')->cascadeOnDelete();
                $table->string('tax_type', 16)->nullable(); // CGST, SGST, IGST, CESS
                $table->string('tax_name', 100);
                $table->string('tax_code', 50)->nullable();
                $table->decimal('tax_rate', 7, 4)->default(0);
                $table->decimal('taxable_amount', 19, 4)->default(0);
                $table->decimal('tax_amount', 19, 4)->default(0);
                $table->timestamps();
                $table->index('order_id', 'order_tax_lines_order_index');
                $table->index('order_item_id', 'order_tax_lines_item_index');
            });
        }

        if (! Schema::hasTable('order_discount_lines')) {
            Schema::create('order_discount_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('order_item_id')->nullable()->constrained('order_items')->cascadeOnDelete();
                $table->string('discount_type', 32); // coupon, manual, scheme, bulk
                $table->string('discount_code', 100)->nullable();
                $table->string('discount_name')->nullable();
                $table->decimal('discount_rate', 7, 4)->nullable();
                $table->decimal('discount_amount', 19, 4)->default(0);
                $table->timestamps();
                $table->index('order_id', 'order_discount_lines_order_index');
                $table->index('order_item_id', 'order_discount_lines_item_index');
            });
        }

        if (! Schema::hasTable('order_adjustments')) {
            Schema::create('order_adjustments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->string('adjustment_type', 32); // shipping, surcharge, roundoff, manual_credit
                $table->string('label');
                $table->decimal('amount', 19, 4)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index('order_id', 'order_adjustments_order_index');
            });
        }

        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('payment_number', 50)->nullable();
                $table->string('provider', 50)->nullable(); // razorpay, stripe, bank_transfer, cash
                $table->string('provider_reference', 100)->nullable();
                $table->string('method', 50)->nullable(); // card, upi, netbanking, cheque, cash
                $table->string('status', 32)->default('pending'); // pending, authorized, paid, failed, refunded
                $table->char('currency', 3)->default('INR');
                $table->decimal('amount', 19, 4)->default(0);
                $table->dateTime('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique('payment_number', 'payments_payment_number_unique');
                $table->index('order_id', 'payments_order_index');
                $table->index('user_id', 'payments_user_index');
            });
        }

        if (! Schema::hasTable('shipments')) {
            Schema::create('shipments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('shipping_address_id')->nullable()->constrained('order_addresses')->nullOnDelete();
                $table->string('shipment_number', 50);
                $table->string('carrier', 100)->nullable();
                $table->string('tracking_number', 100)->nullable();
                $table->string('status', 32)->default('pending'); // pending, packed, shipped, delivered, returned
                $table->dateTime('shipped_at')->nullable();
                $table->dateTime('delivered_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique('shipment_number', 'shipments_shipment_number_unique');
                $table->index('order_id', 'shipments_order_index');
            });
        }

        if (! Schema::hasTable('shipment_items')) {
            Schema::create('shipment_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
                $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
                $table->unsignedInteger('quantity')->default(0);
                $table->timestamps();
                $table->unique(['shipment_id', 'order_item_id'], 'shipment_items_shipment_order_item_unique');
                $table->index('shipment_id', 'shipment_items_shipment_index');
                $table->index('order_item_id', 'shipment_items_order_item_index');
            });
        }

        if (! Schema::hasTable('order_status_history')) {
            Schema::create('order_status_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->string('from_status', 32)->nullable();
                $table->string('to_status', 32);
                $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('remarks')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index('order_id', 'order_status_history_order_index');
            });
        }

    }

    public function down(): void
    {

        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_adjustments');
        Schema::dropIfExists('order_discount_lines');
        Schema::dropIfExists('order_tax_lines');
        Schema::dropIfExists('order_addresses');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
