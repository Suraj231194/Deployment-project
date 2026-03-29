<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
    /**
     * Seed hardcoded companies used by pricing and B2B flows.
     */
    public function run(): void
    {
        $now = now();

        // Step 1: seed fixed company rows so related seeders can safely reference them.
        DB::table('companies')->upsert([
            [
                'id' => 1,
                'name' => 'Biogenix Diagnostics Private Limited',
                'company_type' => 'internal',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Apex Medical Dealers',
                'company_type' => 'dealer',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'City Care Hospital',
                'company_type' => 'hospital',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Precision Labs Private Limited',
                'company_type' => 'lab',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['id'], ['name', 'company_type', 'is_active', 'updated_at']);
    }
}
