<?php

namespace App\Services\Proforma;

use App\Models\Authorization\Company;
use App\Models\Authorization\User;
use App\Models\Product\Product;
use App\Models\Product\UserActivityLog;
use App\Models\Proforma\ProformaInvoice;
use App\Services\Authorization\DataVisibilityService;
use App\Services\Pricing\PriceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProformaInvoiceService
{
    public function __construct(
        protected DataVisibilityService $dataVisibilityService,
        protected PriceService $priceService,
    ) {
    }

    public function getRequestPageData(?User $user, ?int $prefilledProductId = null): array
    {
        // Step 1: load visible products with the price details needed by the PI request page.
        $products = $this->loadVisibleProducts($user);

        // Step 2: load allowed client companies for B2B users.
        $clientCompanies = $this->loadClientCompanies($user);

        return [
            'products' => $products,
            'clientCompanies' => $clientCompanies,
            'prefilledProductId' => $prefilledProductId,
        ];
    }

    public function prepareItems(array $requestData, ?User $user): array
    {
        // Step 1: read the submitted product rows.
        $productIds = is_array($requestData['product_id'] ?? null) ? $requestData['product_id'] : [];
        $quantities = is_array($requestData['quantity'] ?? null) ? $requestData['quantity'] : [];
        $rowCount = max(count($productIds), count($quantities));
        $preparedItems = [];

        // Step 2: build each requested item one by one.
        for ($rowIndex = 0; $rowIndex < $rowCount; $rowIndex++) {
            $productId = (int) ($productIds[$rowIndex] ?? 0);
            $quantity = (int) ($quantities[$rowIndex] ?? 0);

            if ($productId === 0 && $quantity === 0) {
                continue;
            }

            $selectedProduct = $this->findVisibleProduct($user, $productId);

            if (! $selectedProduct) {
                throw ValidationException::withMessages([
                    "product_id.$rowIndex" => 'The selected product is outside your visibility scope.',
                ]);
            }

            $priceDetails = $this->priceService->resolveProductPrice($productId, $user, $quantity);

            if (! $priceDetails) {
                throw ValidationException::withMessages([
                    "product_id.$rowIndex" => 'No visible price is configured for the selected product.',
                ]);
            }

            $this->validateQuantity($quantity, $priceDetails, $rowIndex);
            $preparedItems[] = $this->buildItemData($selectedProduct, $priceDetails, $quantity);
        }

        // Step 3: stop the request when no usable row was submitted.
        if ($preparedItems === []) {
            throw ValidationException::withMessages([
                'product_id' => 'Add at least one PI item.',
            ]);
        }

        return $preparedItems;
    }

    public function calculateTotals(array $preparedItems): array
    {
        // Step 1: add each amount into one PI total summary.
        $subtotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;
        $priceAfterGst = 0;
        $totalAmount = 0;

        foreach ($preparedItems as $preparedItem) {
            $subtotal += (float) $preparedItem['line_subtotal'];
            $taxAmount += (float) $preparedItem['line_tax_amount'];
            $discountAmount += (float) $preparedItem['line_discount_amount'];
            $priceAfterGst += (float) $preparedItem['line_price_after_gst'];
            $totalAmount += (float) $preparedItem['line_total'];
        }

        return [
            'currency' => (string) ($preparedItems[0]['currency'] ?? 'INR'),
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'discount_amount' => round($discountAmount, 2),
            'price_after_gst' => round($priceAfterGst, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }

    public function saveRequestedProforma(
        array $requestData,
        ?User $user,
        array $preparedItems,
        array $requestTotals,
        string $requestNumber,
        string $guestSessionId,
    ): void {
        DB::transaction(function () use ($requestData, $user, $preparedItems, $requestTotals, $requestNumber, $guestSessionId): void {
            // Step 1: save the PI request header with pending review status.
            $proforma = ProformaInvoice::query()->create([
                'pi_number' => $requestNumber,
                'requester_type' => $user ? 'user' : 'guest',
                'created_by_user_id' => $user?->id,
                'owner_user_id' => $user?->id,
                'owner_company_id' => $user?->company_id,
                'target_type' => $requestData['purpose'],
                'target_name' => $requestData['customer_name'],
                'target_email' => $requestData['customer_email'],
                'target_phone' => $requestData['customer_phone'] ?: null,
                'target_company_id' => isset($requestData['target_company_id']) ? (int) $requestData['target_company_id'] : null,
                'status' => 'pending_review',
                'currency' => $requestTotals['currency'],
                'subtotal' => $requestTotals['subtotal'],
                'tax_amount' => $requestTotals['tax_amount'],
                'discount_amount' => $requestTotals['discount_amount'],
                'price_after_gst' => $requestTotals['price_after_gst'],
                'total_amount' => $requestTotals['total_amount'],
                'guest_session_id' => $user ? null : $guestSessionId,
                'notes' => $requestData['notes'] ?: null,
            ]);

            // Step 2: save all requested item lines.
            foreach ($preparedItems as $preparedItem) {
                $proforma->items()->create($preparedItem);
            }
        });
    }

    public function recordRequestSubmitted(?User $user, string $sessionId, string $path, string $requestNumber): void
    {
        // Step 1: store one simple activity log row for the submitted request.
        UserActivityLog::query()->create([
            'session_id' => $sessionId,
            'user_id' => $user?->id,
            'user_type' => $user?->user_type ?: 'guest',
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'activity_type' => 'pi_request_submitted',
            'path' => $path,
            'payload' => ['request_reference' => $requestNumber],
            'created_at' => now(),
        ]);
    }

    protected function loadVisibleProducts(?User $user)
    {
        // Step 1: load the visible products from the current visibility scope.
        $products = $this->dataVisibilityService->visibleProductQuery($user)
            ->orderBy('products.name')
            ->get();

        // Step 2: keep the request page usable even when visible pricing is not ready.
        if ($products->isEmpty()) {
            return $this->loadFallbackProducts();
        }

        // Step 3: attach the resolved price details needed by the page.
        foreach ($products as $product) {
            $priceDetails = $this->priceService->resolveProductPrice((int) $product->id, $user);

            $product->visible_price = $priceDetails['amount'] ?? null;
            $product->visible_currency = $priceDetails['currency'] ?? 'INR';
            $product->visible_price_type = $priceDetails['price_type'] ?? null;
            $product->gst_rate = $priceDetails['gst_rate'] ?? 0;
            $product->tax_amount = $priceDetails['tax_amount'] ?? 0;
            $product->price_after_gst = $priceDetails['price_after_gst'] ?? null;
            $product->min_order_quantity = $priceDetails['min_order_quantity'] ?? 1;
            $product->max_order_quantity = $priceDetails['max_order_quantity'] ?? null;
            $product->lot_size = $priceDetails['lot_size'] ?? 1;
        }

        return $products;
    }

    protected function loadFallbackProducts()
    {
        // Step 1: load active products with their first active sellable variant.
        $products = Product::query()
            ->with([
                'variants' => function ($query): void {
                    $query->where('is_active', true)->orderBy('id');
                },
            ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Step 2: fill the basic product details needed by the page.
        foreach ($products as $product) {
            $primaryVariant = $product->variants->first();

            $product->sku = $product->sku ?: ($primaryVariant?->sku ?? '');
            $product->visible_price = 0.0;
            $product->visible_currency = 'INR';
            $product->visible_price_type = 'manual_review';
            $product->gst_rate = 18.0;
            $product->tax_amount = 0.0;
            $product->price_after_gst = 0.0;
            $product->min_order_quantity = max(1, (int) ($primaryVariant?->min_order_quantity ?? 1));
            $product->max_order_quantity = $primaryVariant?->max_order_quantity;
            $product->lot_size = max(1, (int) ($primaryVariant?->lot_size ?? 1));
        }

        return $products->values();
    }

    protected function loadClientCompanies(?User $user)
    {
        if (! $user || ! $user->isB2b()) {
            return collect();
        }

        // Step 1: load all companies available to the signed-in B2B user.
        $companyIds = $this->dataVisibilityService->assignedClientCompanyIds($user);

        if ($user->company_id) {
            $companyIds[] = $user->company_id;
        }

        return Company::query()
            ->whereIn('id', array_unique($companyIds))
            ->orderBy('name')
            ->get();
    }

    protected function findVisibleProduct(?User $user, int $productId): ?object
    {
        return $this->dataVisibilityService->visibleProductQuery($user)
            ->where('products.id', $productId)
            ->first();
    }

    protected function buildItemData(object $selectedProduct, array $priceDetails, int $quantity): array
    {
        // Step 1: read the unit values from the resolved price row.
        $unitPrice = (float) ($priceDetails['amount'] ?? 0);
        $baseAmount = (float) ($priceDetails['base_amount'] ?? $unitPrice);
        $unitTaxAmount = (float) ($priceDetails['tax_amount'] ?? 0);
        $unitPriceAfterGst = (float) ($priceDetails['price_after_gst'] ?? 0);
        $unitDiscountAmount = round((float) ($priceDetails['discount_amount'] ?? 0), 2);

        // Step 2: calculate the line values for the requested quantity.
        $discountPercent = $baseAmount > 0 ? round(($unitDiscountAmount / $baseAmount) * 100, 2) : 0.00;
        $lineSubtotal = round($unitPrice * $quantity, 2);
        $lineTaxAmount = round($unitTaxAmount * $quantity, 2);
        $linePriceAfterGst = round($unitPriceAfterGst * $quantity, 2);
        $lineDiscountAmount = round($unitDiscountAmount * $quantity, 2);
        $lineTotal = $linePriceAfterGst;

        return [
            'product_id' => (int) $selectedProduct->id,
            'product_variant_id' => $priceDetails['product_variant_id'] ?? null,
            'product_name' => (string) $selectedProduct->name,
            'sku' => (string) ($priceDetails['variant_sku'] ?? $selectedProduct->sku),
            'variant_name' => $priceDetails['variant_name'] ?? null,
            'price_type' => $priceDetails['price_type'] ?? null,
            'currency' => $priceDetails['currency'] ?? 'INR',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'gst_rate' => (float) ($priceDetails['gst_rate'] ?? 0),
            'unit_tax_amount' => $unitTaxAmount,
            'unit_price_after_gst' => $unitPriceAfterGst,
            'discount_percent' => $discountPercent,
            'unit_discount_amount' => $unitDiscountAmount,
            'line_subtotal' => $lineSubtotal,
            'line_tax_amount' => $lineTaxAmount,
            'line_price_after_gst' => $linePriceAfterGst,
            'line_discount_amount' => $lineDiscountAmount,
            'line_total' => $lineTotal,
        ];
    }

    protected function validateQuantity(int $quantity, array $priceDetails, int $rowIndex): void
    {
        // Step 1: read the allowed quantity rules for the selected product.
        $minimumQuantity = max(1, (int) ($priceDetails['min_order_quantity'] ?? 1));
        $maximumQuantity = $priceDetails['max_order_quantity'] ?? null;
        $lotSize = max(1, (int) ($priceDetails['lot_size'] ?? 1));

        // Step 2: stop quantities below the minimum.
        if ($quantity < $minimumQuantity) {
            throw ValidationException::withMessages([
                "quantity.$rowIndex" => "Quantity for item ".($rowIndex + 1)." must be at least {$minimumQuantity}.",
            ]);
        }

        // Step 3: stop quantities above the maximum.
        if ($maximumQuantity !== null && $quantity > (int) $maximumQuantity) {
            throw ValidationException::withMessages([
                "quantity.$rowIndex" => "Quantity for item ".($rowIndex + 1)." must not exceed {$maximumQuantity}.",
            ]);
        }

        // Step 4: stop quantities that do not match the lot size.
        if ($lotSize > 1 && $quantity % $lotSize !== 0) {
            throw ValidationException::withMessages([
                "quantity.$rowIndex" => "Quantity for item ".($rowIndex + 1)." must be in multiples of {$lotSize}.",
            ]);
        }
    }

}
