<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$columns = Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM product_variants');
foreach ($columns as $column) {
    echo $column->Field . PHP_EOL;
}
