<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('faqs')->upsert([
            [
                'id' => 1,
                'category' => 'Product Info',
                'question' => 'What products are available?',
                'answer' => 'We provide IVD kits, reagents, instruments, and consumables tailored for high-throughput diagnostics workflows. Some specialized kits require complete B2B account approval before dispatch.',
                'sort_order' => 10,
                'is_default_open' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'category' => 'Product Info',
                'question' => 'Do you provide technical guidance?',
                'answer' => 'Yes, dedicated onboarding and technical support assistance are available through our specialized technical support team.',
                'sort_order' => 20,
                'is_default_open' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'category' => 'Product Info',
                'question' => 'Are product demos or onboarding sessions available?',
                'answer' => 'Yes, our team can schedule product walkthroughs, onboarding sessions, and basic operating guidance for eligible product lines.',
                'sort_order' => 30,
                'is_default_open' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'category' => 'Ordering Process',
                'question' => 'Can guests generate quotations?',
                'answer' => 'Yes, guests can generate Proforma Invoices with MRP visibility only. To access your custom agreed pricing, please log into your B2B account.',
                'sort_order' => 40,
                'is_default_open' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'category' => 'Ordering Process',
                'question' => 'Do B2B accounts need approval?',
                'answer' => 'Yes, B2B account access is provisioned after administrative review and verification of your organization credentials.',
                'sort_order' => 50,
                'is_default_open' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'category' => 'Ordering Process',
                'question' => 'How do I share a purchase order after quotation approval?',
                'answer' => 'After commercial approval, you can share the final purchase order with our sales team by email so dispatch and invoicing can begin without delay.',
                'sort_order' => 60,
                'is_default_open' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'category' => 'Delivery & Payment',
                'question' => 'Is same-day delivery available?',
                'answer' => 'Same-day logistics support is available for priority locations in and around Lucknow for select product lines.',
                'sort_order' => 70,
                'is_default_open' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'category' => 'Delivery & Payment',
                'question' => 'How are delivery timelines communicated?',
                'answer' => 'Final delivery commitments and expected dates are confirmed directly over email alongside your quote and PO approvals.',
                'sort_order' => 80,
                'is_default_open' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'category' => 'Delivery & Payment',
                'question' => 'What payment options are supported for institutional buyers?',
                'answer' => 'Institutional buyers usually complete payment through agreed bank transfer terms, and payment milestones are confirmed during quotation finalization.',
                'sort_order' => 90,
                'is_default_open' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['id'], ['category', 'question', 'answer', 'sort_order', 'is_default_open', 'is_active', 'updated_at']);
    }
}
