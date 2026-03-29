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
        if (! Schema::hasTable('product_image')) {
            Schema::create('product_image', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('product_id')->nullable();
                $table->string('file_path');
                $table->boolean('is_primary')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['product_id', 'sort_order'], 'product_image_product_sort_index');
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'product_image_id')) {
            try {
                Schema::table('products', function (Blueprint $table): void {
                    $table->foreign('product_image_id')
                        ->references('id')
                        ->on('product_image')
                        ->nullOnDelete();
                });
            } catch (Throwable) {
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'product_image_id')) {
            try {
                Schema::table('products', function (Blueprint $table): void {
                    $table->dropForeign(['product_image_id']);
                });
            } catch (Throwable) {
            }
        }

        if (Schema::hasTable('product_image')) {
            Schema::drop('product_image');
        }
    }
};
