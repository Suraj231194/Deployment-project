<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
foreach (['proforma_invoices', 'proforma_invoice_items'] as $table) {
    echo '[' . $table . ']' . PHP_EOL;
    $columns = Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $table);
    foreach ($columns as $column) {
        echo $column->Field . PHP_EOL;
    }
}
