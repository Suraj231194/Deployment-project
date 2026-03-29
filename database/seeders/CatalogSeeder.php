<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Step 1: seed categories and subcategories used across the catalog.
        DB::table('categories')->upsert([
            [
                'id' => 1,
                'name' => 'Diagnostics',
                'slug' => 'diagnostics',
                'description' => 'Diagnostic kits and testing systems for clinical and laboratory use.',
                'application' => 'Hospitals, pathology labs, diagnostic centers, clinics',
                'IsDisplayedOnHomePage' => true,
                'default_image_path' => 'upload/categories/image1.jpg',
                'gst_rate' => 18.00,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Biochemistry',
                'slug' => 'biochemistry',
                'description' => 'Clinical biochemistry reagents, analyzers, and test kits.',
                'application' => 'Clinical chemistry labs, hospital laboratories, research labs',
                'IsDisplayedOnHomePage' => true,
                'default_image_path' => 'upload/categories/image2.jpg',
                'gst_rate' => 18.00,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Molecular Diagnostics',
                'slug' => 'molecular-diagnostics',
                'description' => 'PCR kits, molecular testing products, and advanced diagnostic solutions.',
                'application' => 'Molecular labs, infectious disease testing, research institutions',
                'IsDisplayedOnHomePage' => true,
                'default_image_path' => 'upload/categories/image3.jpg',
                'gst_rate' => 18.00,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Laboratory Equipment',
                'slug' => 'laboratory-equipment',
                'description' => 'IVD instruments, analyzers, and supporting laboratory equipment.',
                'application' => 'Diagnostic labs, hospitals, medical colleges, research facilities',
                'IsDisplayedOnHomePage' => true,
                'default_image_path' => 'upload/categories/image4.jpg',
                'gst_rate' => 18.00,
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Consumables',
                'slug' => 'consumables',
                'description' => 'Single-use laboratory and medical consumables.',
                'application' => 'Routine lab operations, sample handling, testing workflows',
                'IsDisplayedOnHomePage' => true,
                'default_image_path' => 'upload/categories/image5.jpg',
                'gst_rate' => 12.00,
                'sort_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['id'], ['name', 'slug', 'description', 'application', 'IsDisplayedOnHomePage', 'default_image_path', 'gst_rate', 'sort_order', 'updated_at']);

        DB::table('subcategories')->upsert([
            ['id' => 1, 'category_id' => 1, 'name' => 'ELISA Kits', 'slug' => 'elisa-kits', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'category_id' => 1, 'name' => 'Rapid Test Kits', 'slug' => 'rapid-test-kits', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'category_id' => 1, 'name' => 'Serology Kits', 'slug' => 'serology-kits', 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'category_id' => 1, 'name' => 'Urine Testing Kits', 'slug' => 'urine-testing-kits', 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'category_id' => 2, 'name' => 'Biochemistry Reagents', 'slug' => 'biochemistry-reagents', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'category_id' => 2, 'name' => 'Clinical Chemistry Kits', 'slug' => 'clinical-chemistry-kits', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'category_id' => 2, 'name' => 'Hematology Reagents', 'slug' => 'hematology-reagents', 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'category_id' => 3, 'name' => 'RT-PCR Kits', 'slug' => 'rt-pcr-kits', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'category_id' => 3, 'name' => 'Molecular Products', 'slug' => 'molecular-products', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'category_id' => 3, 'name' => 'Microbiology', 'slug' => 'microbiology', 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'category_id' => 4, 'name' => 'IVD Instruments', 'slug' => 'ivd-instruments', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'category_id' => 4, 'name' => 'Analyzers', 'slug' => 'analyzers', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 13, 'category_id' => 4, 'name' => 'Laboratory Equipment', 'slug' => 'lab-equipment', 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'category_id' => 5, 'name' => 'Blood Collection Consumables', 'slug' => 'blood-collection-consumables', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'category_id' => 5, 'name' => 'Plasticware', 'slug' => 'plasticware', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'category_id' => 5, 'name' => 'General Lab Consumables', 'slug' => 'general-lab-consumables', 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['category_id', 'name', 'slug', 'sort_order', 'updated_at']);

        DB::table('product_specifications')->upsert([
            ['id' => 1, 'specs' => '{"Throughput":"60 tests/hour","Sample Volume":"20uL","Display":"7-inch LCD"}', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'specs' => '{"Tests":"120/hour","Cooling":"Yes","Connectivity":"LIS"}', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'specs' => '{"Detector Type":"Flat Panel","Resolution":"2048 x 2048 pixels","Power":"220V"}', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'specs' => '{"Display":"15-inch Touchscreen","Modes":"2D, Doppler, M-Mode"}', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'specs' => '{"Leads":"12-lead","Display":"5-inch LCD","Battery":"Rechargeable"}', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'specs' => '{"Material":"Nitrile","Powder Free":"Yes","Usage":"Single use"}', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'specs' => '{"Sterile":"Yes","Capacity":"5ml","Pack Size":"50"}', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['specs', 'updated_at']);

        DB::table('product_image')->upsert([
            ['id' => 1,'file_path' => 'upload/products/hema1.jpg', 'is_primary' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'file_path' => 'upload/products/hema2.jpg', 'is_primary' => false, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3,  'file_path' => 'upload/products/image1.jpg', 'is_primary' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'file_path' => 'upload/products/image2.jpg', 'is_primary' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'file_path' => 'upload/products/image3.jpg', 'is_primary' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'file_path' => 'upload/products/image4.jpg', 'is_primary' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'file_path' => 'upload/products/image5.jpg', 'is_primary' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8,  'file_path' => 'upload/products/image1.jpg', 'is_primary' => false, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'file_path' => 'upload/products/image2.jpg', 'is_primary' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['file_path', 'is_primary', 'sort_order', 'updated_at']);
        // Step 2: seed product master data, including the business-owned overview used on the detail page.
        DB::table('products')->upsert([
            ['id' => 1, 'name' => 'Hematology Analyzer X200', 'slug' => 'hematology-analyzer-x200', 'description' => 'High-precision hematology analyzer for labs.', 'badges' => 'In Stock,Verified', 'product_overview' => 'The Hematology Analyzer X200 is designed for laboratories that need dependable CBC processing with steady throughput and clear reporting. It supports routine pathology workflows where operators need simple daily use, reliable sample handling, and consistent diagnostic output.', 'category_id' => 1, 'subcategory_id' => 1, 'base_sku' => 'BIO-HEMA', 'is_published' => true, 'product_image_id' => 1, 'product_specifications_id' => 1, 'sku' => 'BIO-HEMA-X200', 'brand' => 'Biogenix', 'gst_rate' => null, 'visibility_scope' => 'public', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Biochemistry Analyzer B500', 'slug' => 'biochemistry-analyzer-b500', 'description' => 'Fully automated biochemistry analyzer.', 'badges' => 'Clinical Ready,Best Seller', 'product_overview' => 'The Biochemistry Analyzer B500 is built for diagnostic centers that want automated chemistry testing with faster turnaround and reduced manual dependency. It fits well in busy lab operations where teams need stable analyzer performance, efficient reagent use, and dependable daily productivity.', 'category_id' => 1, 'subcategory_id' => 2, 'base_sku' => 'BIO-BIOC', 'is_published' => true, 'product_image_id' => 2, 'product_specifications_id' => 2, 'sku' => 'BIO-BIOC-B500', 'brand' => 'Biogenix', 'gst_rate' => null, 'visibility_scope' => 'b2b', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Digital X-Ray Machine DXR-1000', 'slug' => 'digital-xray-machine-dxr-1000', 'description' => 'High-resolution digital X-ray imaging system.', 'badges' => 'New Arrival,Clinical Ready', 'product_overview' => 'The Digital X-Ray Machine DXR-1000 supports imaging teams that need sharp image capture, dependable detector performance, and smooth equipment operation during routine patient studies. It is suited for healthcare facilities that want digital imaging capability with a professional-grade installation footprint.', 'category_id' => 2, 'subcategory_id' => 3, 'base_sku' => 'BIO-DXR', 'is_published' => true, 'product_image_id' => 3, 'product_specifications_id' => 3, 'sku' => 'BIO-DXR-1000', 'brand' => 'Biogenix', 'gst_rate' => null, 'visibility_scope' => 'b2b', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Ultrasound Machine UltraSound Pro', 'slug' => 'ultrasound-machine-ultrasound-pro', 'description' => 'Advanced ultrasound system for detailed imaging.', 'badges' => 'Best Seller,Institutional Fav', 'product_overview' => 'The UltraSound Pro is intended for clinicians and imaging departments that need flexible scanning modes and clear visual output across routine examinations. It helps teams manage daily diagnostic imaging work with a system that balances feature depth, usability, and dependable clinical support.', 'category_id' => 2, 'subcategory_id' => 4, 'base_sku' => 'BIO-USP', 'is_published' => true, 'product_image_id' => 4, 'product_specifications_id' => 4, 'sku' => 'BIO-USP-PRO', 'brand' => 'Biogenix', 'gst_rate' => null, 'visibility_scope' => 'b2b', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'ECG Machine CardioMax 3000', 'slug' => 'ecg-machine-cardiomax-3000', 'description' => 'Portable ECG machine with advanced features.', 'badges' => 'In Stock,Best Seller', 'product_overview' => 'The CardioMax 3000 is made for cardiac screening and routine monitoring environments where portability and dependable waveform capture matter. It serves hospitals, clinics, and diagnostic rooms that need a simple ECG workflow with dependable day-to-day performance.', 'category_id' => 3, 'subcategory_id' => 5, 'base_sku' => 'BIO-ECG', 'is_published' => true, 'product_image_id' => 5, 'product_specifications_id' => 5, 'sku' => 'BIO-ECG-3000', 'brand' => 'Biogenix', 'gst_rate' => null, 'visibility_scope' => 'public', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'Nitrile Gloves', 'slug' => 'nitrile-gloves', 'description' => 'Single-use gloves for basic lab and hospital procedures.', 'badges' => 'Verified,Best Seller', 'product_overview' => 'These Nitrile Gloves are intended for routine handling, sample processing, and daily protection needs in hospital and laboratory environments. They support teams that need disposable safety consumables with practical comfort, barrier protection, and dependable availability for repeat use.', 'category_id' => 4, 'subcategory_id' => 6, 'base_sku' => 'BIO-GLV', 'is_published' => true, 'product_image_id' => 5, 'product_specifications_id' => 6, 'sku' => 'BIO-GLV-001', 'brand' => 'Biogenix', 'gst_rate' => 5.00, 'visibility_scope' => 'public', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'name' => 'Sterile Syringe Pack', 'slug' => 'sterile-syringe-pack', 'description' => 'Common consumable visible for guest, B2C, and B2B.', 'badges' => 'In Stock,New Arrival', 'product_overview' => 'The Sterile Syringe Pack supports everyday injection, sample transfer, and procedure preparation needs across healthcare and laboratory operations. It is suitable for buyers who need a reliable consumable item that is easy to procure, easy to store, and ready for regular operational use.', 'category_id' => 4, 'subcategory_id' => 7, 'base_sku' => 'BIO-SYR', 'is_published' => true, 'product_image_id' => 6, 'product_specifications_id' => 7, 'sku' => 'BIO-SYR-050', 'brand' => 'Biogenix', 'gst_rate' => null, 'visibility_scope' => 'public', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['name', 'slug', 'description', 'badges', 'product_overview', 'category_id', 'subcategory_id', 'base_sku', 'is_published', 'product_image_id', 'product_specifications_id', 'sku', 'brand', 'gst_rate', 'visibility_scope', 'is_active', 'updated_at']);

        // Step 3: each product has at least one variant, and each variant carries the technical specs shown on the detail page.
        DB::table('product_variants')->upsert([
            ['id' => 1, 'product_id' => 1, 'sku' => 'BIO-HEMA-X200-STD', 'variant_name' => 'Standard', 'technical_specification_json' => '{"Throughput":"60 tests/hour","Sample Volume":"20 uL","Display":"7-inch LCD"}', 'min_order_quantity' => 1, 'max_order_quantity' => 20, 'b2c_min_order_quantity' => 1, 'b2c_max_order_quantity' => 2, 'b2b_min_order_quantity' => 1, 'b2b_max_order_quantity' => 20, 'lot_size' => 1, 'model_number' => 'X200-STD', 'catalog_number' => 'CAT-HEMA-001', 'stock_quantity' => 8, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'product_id' => 1, 'sku' => 'BIO-HEMA-X200-PRO', 'variant_name' => 'Pro', 'technical_specification_json' => '{"Throughput":"90 tests/hour","Sample Volume":"15 uL","Display":"10-inch LCD"}', 'min_order_quantity' => 1, 'max_order_quantity' => 20, 'b2c_min_order_quantity' => 1, 'b2c_max_order_quantity' => 2, 'b2b_min_order_quantity' => 1, 'b2b_max_order_quantity' => 20, 'lot_size' => 1, 'model_number' => 'X200-PRO', 'catalog_number' => 'CAT-HEMA-002', 'stock_quantity' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'product_id' => 2, 'sku' => 'BIO-BIOC-B500-STD', 'variant_name' => 'Standard', 'technical_specification_json' => '{"Tests":"120 per hour","Cooling":"Yes","Connectivity":"LIS ready"}', 'min_order_quantity' => 1, 'max_order_quantity' => 12, 'b2c_min_order_quantity' => 1, 'b2c_max_order_quantity' => 1, 'b2b_min_order_quantity' => 1, 'b2b_max_order_quantity' => 12, 'lot_size' => 1, 'model_number' => 'B500-STD', 'catalog_number' => 'CAT-BIOC-001', 'stock_quantity' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'product_id' => 3, 'sku' => 'BIO-DXR-1000-BASE', 'variant_name' => 'Base', 'technical_specification_json' => '{"Detector":"Flat panel","Resolution":"2048 x 2048 pixels","Power":"220V"}', 'min_order_quantity' => 1, 'max_order_quantity' => 6, 'b2c_min_order_quantity' => 1, 'b2c_max_order_quantity' => 1, 'b2b_min_order_quantity' => 1, 'b2b_max_order_quantity' => 6, 'lot_size' => 1, 'model_number' => 'DXR-1000', 'catalog_number' => 'CAT-DXR-001', 'stock_quantity' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'product_id' => 4, 'sku' => 'BIO-USP-PRO-BASE', 'variant_name' => 'Base', 'technical_specification_json' => '{"Display":"15-inch touchscreen","Modes":"2D, Doppler, M-Mode","Power":"220V"}', 'min_order_quantity' => 1, 'max_order_quantity' => 8, 'b2c_min_order_quantity' => 1, 'b2c_max_order_quantity' => 1, 'b2b_min_order_quantity' => 1, 'b2b_max_order_quantity' => 8, 'lot_size' => 1, 'model_number' => 'USP-PRO', 'catalog_number' => 'CAT-USP-001', 'stock_quantity' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'product_id' => 5, 'sku' => 'BIO-ECG-3000-BASE', 'variant_name' => 'Base', 'technical_specification_json' => '{"Leads":"12-lead","Display":"5-inch LCD","Battery":"Rechargeable"}', 'min_order_quantity' => 1, 'max_order_quantity' => 25, 'b2c_min_order_quantity' => 1, 'b2c_max_order_quantity' => 3, 'b2b_min_order_quantity' => 1, 'b2b_max_order_quantity' => 25, 'lot_size' => 1, 'model_number' => 'ECG-3000', 'catalog_number' => 'CAT-ECG-001', 'stock_quantity' => 12, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'product_id' => 6, 'sku' => 'BIO-GLV-001-100', 'variant_name' => 'Pack 100', 'technical_specification_json' => '{"Material":"Nitrile","Powder Free":"Yes","Pack Size":"100 units"}', 'min_order_quantity' => 10, 'max_order_quantity' => 500, 'b2c_min_order_quantity' => 10, 'b2c_max_order_quantity' => 200, 'b2b_min_order_quantity' => 20, 'b2b_max_order_quantity' => 500, 'lot_size' => 10, 'model_number' => 'GLV-100', 'catalog_number' => 'CAT-GLV-001', 'stock_quantity' => 350, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'product_id' => 7, 'sku' => 'BIO-SYR-050-50', 'variant_name' => 'Pack 50', 'technical_specification_json' => '{"Sterile":"Yes","Capacity":"5 ml","Pack Size":"50 units"}', 'min_order_quantity' => 10, 'max_order_quantity' => 500, 'b2c_min_order_quantity' => 10, 'b2c_max_order_quantity' => 150, 'b2b_min_order_quantity' => 20, 'b2b_max_order_quantity' => 500, 'lot_size' => 10, 'model_number' => 'SYR-050', 'catalog_number' => 'CAT-SYR-001', 'stock_quantity' => 500, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['product_id', 'sku', 'variant_name', 'technical_specification_json', 'min_order_quantity', 'max_order_quantity', 'b2c_min_order_quantity', 'b2c_max_order_quantity', 'b2b_min_order_quantity', 'b2b_max_order_quantity', 'lot_size', 'model_number', 'catalog_number', 'stock_quantity', 'is_active', 'updated_at']);

        // Step 4: save GST and final price directly on each price row.
        $variantGstRates = [
            1 => 18.00,
            2 => 18.00,
            3 => 18.00,
            4 => 18.00,
            5 => 18.00,
            6 => 18.00,
            7 => 5.00,
            8 => 12.00,
        ];

        // Step 5: keep one small helper so seed prices only store pricing data while GST totals stay ready for use.
        $buildSeedPriceRow = function (array $row) use ($variantGstRates): array {
            $gstRate = $variantGstRates[$row['product_variant_id']] ?? 0;
            $row['DiscountType'] = $row['DiscountType'] ?? 'cash';
            $row['Discount'] = $row['Discount'] ?? 0.00;
            $taxAmount = round(($row['amount'] * $gstRate) / 100, 2);
            $row['gst_rate'] = $gstRate;
            $row['tax_amount'] = $taxAmount;
            $row['price_after_gst'] = round($row['amount'] + $taxAmount, 2);

            return $row;
        };

        $genericPriceRows = [
            ['product_variant_id' => 1, 'price_type' => 'retail', 'company_id' => null, 'amount' => 265000.00, 'DiscountType' => 'percent', 'Discount' => 5.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 1, 'price_type' => 'logged_in', 'company_id' => null, 'amount' => 255000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 1, 'price_type' => 'dealer', 'company_id' => null, 'amount' => 245000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 1, 'price_type' => 'institutional', 'company_id' => null, 'amount' => 248000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 2, 'price_type' => 'retail', 'company_id' => null, 'amount' => 285000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            ['product_variant_id' => 3, 'price_type' => 'retail', 'company_id' => null, 'amount' => 390000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 3, 'price_type' => 'logged_in', 'company_id' => null, 'amount' => 378000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 3, 'price_type' => 'dealer', 'company_id' => null, 'amount' => 368000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 3, 'price_type' => 'institutional', 'company_id' => null, 'amount' => 372000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            ['product_variant_id' => 4, 'price_type' => 'retail', 'company_id' => null, 'amount' => 1550000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 4, 'price_type' => 'logged_in', 'company_id' => null, 'amount' => 1490000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 4, 'price_type' => 'dealer', 'company_id' => null, 'amount' => 1450000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            ['product_variant_id' => 5, 'price_type' => 'retail', 'company_id' => null, 'amount' => 870000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 5, 'price_type' => 'logged_in', 'company_id' => null, 'amount' => 840000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 5, 'price_type' => 'institutional', 'company_id' => null, 'amount' => 825000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            ['product_variant_id' => 6, 'price_type' => 'public', 'company_id' => null, 'amount' => 120000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 6, 'price_type' => 'retail', 'company_id' => null, 'amount' => 126000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 6, 'price_type' => 'logged_in', 'company_id' => null, 'amount' => 122000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            ['product_variant_id' => 7, 'price_type' => 'public', 'company_id' => null, 'amount' => 120.00, 'DiscountType' => 'percent', 'Discount' => 5.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 7, 'price_type' => 'retail', 'company_id' => null, 'amount' => 130.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 7, 'price_type' => 'logged_in', 'company_id' => null, 'amount' => 125.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],

            ['product_variant_id' => 8, 'price_type' => 'public', 'company_id' => null, 'amount' => 80.00, 'DiscountType' => 'cash', 'Discount' => 4.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 8, 'price_type' => 'retail', 'company_id' => null, 'amount' => 95.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['product_variant_id' => 8, 'price_type' => 'logged_in', 'company_id' => null, 'amount' => 88.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        // Step 6: refresh demo price rows for the seeded variants so rerunning the seeder restores the current commercial setup cleanly.
        DB::table('product_prices')->whereIn('product_variant_id', range(1, 8))->delete();
        DB::table('product_prices')->insert(array_map($buildSeedPriceRow, $genericPriceRows));

        // Optional example: client-specific company pricing.
        $firstCompanyId = DB::table('companies')->value('id');
        if ($firstCompanyId) {
            $companyPriceRows = [
                ['product_variant_id' => 1, 'price_type' => 'company_price', 'company_id' => $firstCompanyId, 'amount' => 238000.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['product_variant_id' => 6, 'price_type' => 'company_price', 'company_id' => $firstCompanyId, 'amount' => 116500.00, 'currency' => 'INR', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ];

            DB::table('product_prices')->insert(array_map($buildSeedPriceRow, $companyPriceRows));
        }

        DB::table('variant_attributes')->upsert([
            ['id' => 1, 'product_variant_id' => 1, 'attribute_name' => 'Throughput', 'attribute_value' => '60 tests/hour', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'product_variant_id' => 2, 'attribute_name' => 'Throughput', 'attribute_value' => '90 tests/hour', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'product_variant_id' => 3, 'attribute_name' => 'Tests', 'attribute_value' => '120/hour', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'product_variant_id' => 4, 'attribute_name' => 'Detector', 'attribute_value' => 'Flat Panel', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'product_variant_id' => 5, 'attribute_name' => 'Modes', 'attribute_value' => '2D, Doppler, M-Mode', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'product_variant_id' => 6, 'attribute_name' => 'Leads', 'attribute_value' => '12-lead', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'product_variant_id' => 7, 'attribute_name' => 'Pack Size', 'attribute_value' => '100 units', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'product_variant_id' => 8, 'attribute_name' => 'Pack Size', 'attribute_value' => '50 units', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['product_variant_id', 'attribute_name', 'attribute_value', 'updated_at']);
    }
}
