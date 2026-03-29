<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingRulesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Step 1: seed a small set of real bulk pricing examples so the shared pricing service has live tiers to resolve.
        DB::table('product_bulk_prices')->insert([
            ['product_variant_id' => 1, 'user_id' => null, 'role_id' => null, 'applies_to_user_type' => 'b2b', 'min_quantity' => 5, 'max_quantity' => 9, 'amount' => 240000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 1, 'user_id' => null, 'role_id' => null, 'applies_to_user_type' => 'b2b', 'min_quantity' => 10, 'max_quantity' => null, 'amount' => 232000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 7, 'user_id' => null, 'role_id' => null, 'applies_to_user_type' => null, 'min_quantity' => 50, 'max_quantity' => 99, 'amount' => 112.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 7, 'user_id' => null, 'role_id' => null, 'applies_to_user_type' => null, 'min_quantity' => 100, 'max_quantity' => null, 'amount' => 105.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 8, 'user_id' => null, 'role_id' => null, 'applies_to_user_type' => 'b2b', 'min_quantity' => 100, 'max_quantity' => null, 'amount' => 72.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Step 2: seed a couple of coupons with different stacking rules so checkout can use real backend coupon validation.
        DB::table('coupons')->insert([
            ['code' => 'BIO10', 'discount_type' => 'percent', 'discount_value' => 10.00, 'allow_with_bulk' => false, 'allow_with_product_discount' => false, 'allow_on_company_price' => false, 'is_active' => true, 'valid_from' => null, 'valid_to' => null, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'SAVE5', 'discount_type' => 'percent', 'discount_value' => 5.00, 'allow_with_bulk' => true, 'allow_with_product_discount' => true, 'allow_on_company_price' => false, 'is_active' => true, 'valid_from' => null, 'valid_to' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
