<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$service = app(App\Services\Authorization\DataVisibilityService::class);
$products = $service->visibleProductQuery(null)->get(['products.id','products.name']);
echo 'visible=' . $products->count() . PHP_EOL;
foreach ($products as $product) {
    echo $product->id . ':' . $product->name . PHP_EOL;
}
