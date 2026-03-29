<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportTicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('support_ticket_categories')->upsert([
            [
                'id' => 1,
                'name' => 'Technical',
                'slug' => 'technical',
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Billing',
                'slug' => 'billing',
                'is_active' => true,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Account',
                'slug' => 'account',
                'is_active' => true,
                'sort_order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'General',
                'slug' => 'general',
                'is_active' => true,
                'sort_order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Other',
                'slug' => 'other',
                'is_active' => true,
                'sort_order' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['id'], ['name', 'slug', 'is_active', 'sort_order', 'updated_at']);
    }
}
