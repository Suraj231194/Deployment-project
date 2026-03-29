<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$service = app(App\Services\ProformaInvoiceService::class);
$data = $service->createPageData(null, null);
echo 'products=' . count($data['products']) . PHP_EOL;
foreach ($data['products'] as $product) {
    echo $product->id . ':' . $product->name . '|' . ($product->visible_price_type ?? 'null') . '|' . ($product->price_after_gst ?? 'null') . PHP_EOL;
}
