<?php

namespace Database\Seeders;

use App\Services\Utility\FileHandlingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTechnicalResourceSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Step 1: prepare a small hardcoded document set so the product detail page has real technical downloads in demo data.
        $technicalResources = [
            [
                'id' => 1,
                'product_id' => 1,
                'product_variant_id' => 1,
                'title' => 'Certificate of Analysis',
                'resource_type' => 'certificate_of_analysis',
                'description' => 'Batch-linked quality document',
                'stored_file_path' => 'upload/documents/products/1/technical-resources/certificate-of-analysis.pdf',
                'original_file_name' => 'Certificate of Analysis.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 0,
                'sort_order' => 1,
                'is_active' => true,
                'created_by_user_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'product_id' => 1,
                'product_variant_id' => 1,
                'title' => 'Safety Data Sheet',
                'resource_type' => 'safety_data_sheet',
                'description' => 'Handling and compliance reference',
                'stored_file_path' => 'upload/documents/products/1/technical-resources/safety-data-sheet.pdf',
                'original_file_name' => 'Safety Data Sheet.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 0,
                'sort_order' => 2,
                'is_active' => true,
                'created_by_user_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'product_id' => 1,
                'product_variant_id' => 1,
                'title' => 'User Manual',
                'resource_type' => 'user_manual',
                'description' => 'Installation and usage guide',
                'stored_file_path' => 'upload/documents/products/1/technical-resources/user-manual.pdf',
                'original_file_name' => 'User Manual.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 0,
                'sort_order' => 3,
                'is_active' => true,
                'created_by_user_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'product_id' => 1,
                'product_variant_id' => 1,
                'title' => 'Maintenance Schedule',
                'resource_type' => 'maintenance_schedule',
                'description' => 'Standard care checklist',
                'stored_file_path' => 'upload/documents/products/1/technical-resources/maintenance-schedule.pdf',
                'original_file_name' => 'Maintenance Schedule.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 0,
                'sort_order' => 4,
                'is_active' => true,
                'created_by_user_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $fileHandlingService = app(FileHandlingService::class);

        // Step 2: create placeholder files so download buttons work immediately after the seed runs.
        foreach ($technicalResources as &$technicalResource) {
            $fileContents = implode("\n", [
                'Biogenix Technical Resource',
                'Title: '.$technicalResource['title'],
                'Product ID: '.$technicalResource['product_id'],
                'This is a seeded placeholder file for testing the technical resource download flow.',
            ]);

            // Step 3: write the seeded placeholder file through the shared helper so document location stays centralized.
            $fileHandlingService->writePublicFile($technicalResource['stored_file_path'], $fileContents);
            $technicalResource['file_size'] = $fileHandlingService->fileSize($technicalResource['stored_file_path']);
        }
        unset($technicalResource);

        // Step 4: refresh the demo technical resource rows so reseeding keeps the product detail page predictable.
        DB::table('product_technical_resources')->whereIn('id', collect($technicalResources)->pluck('id'))->delete();
        DB::table('product_technical_resources')->insert($technicalResources);
    }
}
