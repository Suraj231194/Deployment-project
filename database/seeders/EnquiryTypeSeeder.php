<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnquiryTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Business step: keep the enquiry type master simple and predictable with fixed setup values.
        DB::table('enquiry_types')->upsert([
            [
                'id' => 1,
                'name' => 'Product Information',
                'slug' => 'product-information',
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Generate Quotation',
                'slug' => 'generate-quotation',
                'is_active' => true,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Partnership',
                'slug' => 'partnership',
                'is_active' => true,
                'sort_order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'is_active' => true,
                'sort_order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['id'], ['name', 'slug', 'is_active', 'sort_order', 'updated_at']);
    }
}
