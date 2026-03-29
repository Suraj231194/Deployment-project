<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Step 1: seed the reward coupon used by the quiz result flow.
        DB::table('coupons')->upsert([
            [
                'code' => 'BIOGENIX15',
                'discount_type' => 'percent',
                'discount_value' => 15.00,
                'allow_with_bulk' => false,
                'allow_with_product_discount' => false,
                'allow_on_company_price' => false,
                'is_active' => true,
                'valid_from' => null,
                'valid_to' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['code'], ['discount_type', 'discount_value', 'allow_with_bulk', 'allow_with_product_discount', 'allow_on_company_price', 'is_active', 'valid_from', 'valid_to', 'updated_at']);

        // Step 2: seed the fixed diagnostic quiz questions used by the public quiz page.
        DB::table('diagnostic_quiz_questions')->upsert([
            [
                'id' => 1,
                'phase_title' => 'Biogenix Kits Mastery',
                'question_text' => 'Which reagent kit is best suited for high-throughput automation?',
                'question_support_details' => json_encode([
                    'show_question_badge' => true,
                    'sidebar_cards' => [
                        [
                            'card_type' => 'tip',
                            'eyebrow' => 'Clinical Tip',
                            'title' => 'Automation Integration',
                            'description' => 'Automation-compatible kits utilize standard SBS footprints and barcoded vials. When selecting a kit for high-throughput environments, prioritize those with liquid-level sensing compatibility to minimize aspiration errors.',
                        ],
                        [
                            'card_type' => 'context_list',
                            'title' => 'Assessment Context',
                            'items' => [
                                ['icon' => 'document', 'text' => 'Module: Reagent Classification'],
                                ['icon' => 'check-circle', 'text' => 'Difficulty: Intermediate'],
                                ['icon' => 'clock', 'text' => 'Time Limit: No constraints'],
                            ],
                        ],
                        [
                            'card_type' => 'image',
                            'image_path' => 'upload/corousel/image3.jpg',
                            'image_alt_text' => 'Automated pipetting system',
                            'image_caption' => 'Fig 1.1: Automated pipetting system with Biogenix reagents.',
                        ],
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'display_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'phase_title' => 'Biogenix Kits Proficiency',
                'question_text' => 'What is the required storage temperature for the DNA Polymerase High Fidelity kit?',
                'question_support_details' => json_encode([
                    'show_question_badge' => false,
                    'sidebar_cards' => [
                        [
                            'card_type' => 'context_sections',
                            'title' => 'Storage Best Practices',
                            'sections' => [
                                [
                                    'title' => 'Enzymatic Stability',
                                    'description' => 'Most high-fidelity polymerases lose activity if exposed to repeated freeze-thaw cycles. Always use a cooling block during use.',
                                ],
                                [
                                    'title' => 'Reagent Segregation',
                                    'description' => 'Keep dNTPs and primers in separate aliquots to prevent cross-contamination during library preparation.',
                                ],
                            ],
                        ],
                        [
                            'card_type' => 'insight',
                            'eyebrow' => 'Clinical Insight',
                            'description' => 'Storing at -20°C in a non-frost-free freezer is critical for maintaining long-term buffer molarity.',
                        ],
                        [
                            'card_type' => 'reference_list',
                            'title' => 'Reference Material',
                            'items' => [
                                [
                                    'document_name' => 'Kit_Datasheet_V4.pdf',
                                    'leading_icon' => 'file-blue',
                                    'trailing_icon' => 'download',
                                ],
                                [
                                    'document_name' => 'Storage_Protocol_Guide',
                                    'leading_icon' => 'file-green',
                                    'trailing_icon' => 'external-link',
                                ],
                            ],
                        ],
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'display_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'phase_title' => 'Compliance Standards',
                'question_text' => 'Which certification standard governs IVD reagent manufacturing quality?',
                'question_support_details' => json_encode([
                    'show_question_badge' => true,
                    'sidebar_cards' => [
                        [
                            'card_type' => 'tip',
                            'eyebrow' => 'Clinical Tip',
                            'title' => 'Regulatory Compliance',
                            'description' => 'ISO 13485 is the primary quality management standard for medical devices and IVD products. It ensures traceability, risk management, and process validation throughout the product lifecycle.',
                        ],
                        [
                            'card_type' => 'context_list',
                            'title' => 'Assessment Context',
                            'items' => [
                                ['icon' => 'document', 'text' => 'Module: Compliance Standards'],
                                ['icon' => 'check-circle', 'text' => 'Difficulty: Advanced'],
                            ],
                        ],
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'display_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'phase_title' => 'Sample Preparation',
                'question_text' => 'Which sample preparation method yields highest DNA purity for NGS workflows?',
                'question_support_details' => json_encode([
                    'show_question_badge' => true,
                    'sidebar_cards' => [
                        [
                            'card_type' => 'tip',
                            'eyebrow' => 'Clinical Tip',
                            'title' => 'NGS Library Prep',
                            'description' => 'Magnetic bead-based purification provides the best combination of purity and automation compatibility for next-generation sequencing, with minimal carry-over contamination.',
                        ],
                        [
                            'card_type' => 'image',
                            'image_path' => 'upload/corousel/image5.jpg',
                            'image_alt_text' => 'NGS sample preparation',
                            'image_caption' => 'Fig 4.1: NGS library preparation workflow.',
                        ],
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'display_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['id'], ['phase_title', 'question_text', 'question_support_details', 'display_order', 'updated_at']);

        // Step 3: seed the answer options used for scoring and display.
        DB::table('diagnostic_quiz_answer_options')->upsert([
            ['id' => 1, 'question_id' => 1, 'option_label' => 'A', 'option_text' => 'Precision-X LIMS Kit', 'is_correct_answer' => true, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'question_id' => 1, 'option_label' => 'B', 'option_text' => 'Bio-RGT Standard', 'is_correct_answer' => false, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'question_id' => 1, 'option_label' => 'C', 'option_text' => 'Clinical-Max Assay', 'is_correct_answer' => false, 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'question_id' => 1, 'option_label' => 'D', 'option_text' => 'Eco-Lite Consumable', 'is_correct_answer' => false, 'display_order' => 4, 'created_at' => $now, 'updated_at' => $now],

            ['id' => 5, 'question_id' => 2, 'option_label' => 'A', 'option_text' => 'Room Temperature', 'is_correct_answer' => false, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'question_id' => 2, 'option_label' => 'B', 'option_text' => '4°C', 'is_correct_answer' => false, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'question_id' => 2, 'option_label' => 'C', 'option_text' => '-20°C', 'is_correct_answer' => true, 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'question_id' => 2, 'option_label' => 'D', 'option_text' => '-80°C', 'is_correct_answer' => false, 'display_order' => 4, 'created_at' => $now, 'updated_at' => $now],

            ['id' => 9, 'question_id' => 3, 'option_label' => 'A', 'option_text' => 'ISO 9001:2015', 'is_correct_answer' => false, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'question_id' => 3, 'option_label' => 'B', 'option_text' => 'ISO 13485:2016', 'is_correct_answer' => true, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'question_id' => 3, 'option_label' => 'C', 'option_text' => 'CE-IVD Directive 98/79/EC', 'is_correct_answer' => false, 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'question_id' => 3, 'option_label' => 'D', 'option_text' => 'GMP Annex 15', 'is_correct_answer' => false, 'display_order' => 4, 'created_at' => $now, 'updated_at' => $now],

            ['id' => 13, 'question_id' => 4, 'option_label' => 'A', 'option_text' => 'Phenol-chloroform extraction', 'is_correct_answer' => false, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'question_id' => 4, 'option_label' => 'B', 'option_text' => 'Magnetic bead-based purification', 'is_correct_answer' => true, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'question_id' => 4, 'option_label' => 'C', 'option_text' => 'Silica membrane column', 'is_correct_answer' => false, 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'question_id' => 4, 'option_label' => 'D', 'option_text' => 'Salting-out precipitation', 'is_correct_answer' => false, 'display_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['question_id', 'option_label', 'option_text', 'is_correct_answer', 'display_order', 'updated_at']);
    }
}
