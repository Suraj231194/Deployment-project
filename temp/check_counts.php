<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo 'products=' . App\Models\Product\Product::count() . PHP_EOL;
echo 'variants=' . App\Models\Product\ProductVariant::count() . PHP_EOL;
echo 'prices=' . App\Models\Product\ProductPrice::count() . PHP_EOL;
