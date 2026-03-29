<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RbacSeeder::class,
            CompaniesSeeder::class,
            AdminSetupSeeder::class,
            CatalogSeeder::class,
            SupportTicketCategorySeeder::class,
            EnquiryTypeSeeder::class,
            FaqSeeder::class,
            ProductTechnicalResourceSeeder::class,
            PricingRulesSeeder::class,
            QuizeSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
