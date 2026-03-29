<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$proforma = App\Models\Proforma\ProformaInvoice::query()->latest('id')->first();
if (! $proforma) {
    echo 'no-proforma';
    exit;
}
$proforma = App\Models\Proforma\ProformaInvoice::query()->with(['items', 'creator', 'ownerUser', 'ownerCompany', 'targetCompany'])->find($proforma->id);
$response = app(App\Services\ProformaInvoiceService::class)->downloadProformaPdf($proforma);
echo get_class($response) . PHP_EOL;
echo strlen($response->getContent()) . PHP_EOL;
