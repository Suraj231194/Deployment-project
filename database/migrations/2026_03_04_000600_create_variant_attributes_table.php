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
        if (Schema::hasTable('variant_attributes')) {
            return;
        }

        Schema::create('variant_attributes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('attribute_name');
            $table->string('attribute_value');
            $table->timestamps();

            $table->index(['product_variant_id', 'attribute_name'], 'variant_attribute_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('variant_attributes')) {
            Schema::drop('variant_attributes');
        }
    }
};
