<?php

namespace App\Services\Quotation;

use App\Models\Authorization\Company;
use App\Models\Authorization\User;
use App\Models\Product\Product;
use App\Models\Quotation\Quotation;
use App\Services\Authorization\DataVisibilityService;
use App\Services\Pricing\PriceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class QuotationService
{
    public function __construct(
        protected DataVisibilityService $dataVisibilityService,
        protected PriceService $priceService,
    ) {
    }

    public function getCreatePageData(?User $user, ?int $prefilledProductId = null): array
    {
        // Step 1: load visible products with the price details needed by the quotation page.
        $products = $this->loadVisibleProducts($user);

        // Step 2: load allowed client companies for B2B users.
        $clientCompanies = $this->loadClientCompanies($user);

        return [
            'products' => $products,
            'clientCompanies' => $clientCompanies,
            'prefilledProductId' => $prefilledProductId,
        ];
    }

    public function prepareItems(array $quotationData, ?User $user): array
    {
        // Step 1: read the submitted quotation rows.
        $productIds = is_array($quotationData['product_id'] ?? null) ? $quotationData['product_id'] : [];
        $quantities = is_array($quotationData['quantity'] ?? null) ? $quotationData['quantity'] : [];
        $rowCount = max(count($productIds), count($quantities));
        $preparedItems = [];

        // Step 2: build each quotation item one by one.
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

        // Step 3: stop the save when no usable row was submitted.
        if ($preparedItems === []) {
            throw ValidationException::withMessages([
                'product_id' => 'Add at least one quotation item.',
            ]);
        }

        return $preparedItems;
    }

    public function calculateTotals(array $preparedItems): array
    {
        // Step 1: add each amount into one quotation total summary.
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

    public function saveQuotation(
        array $quotationData,
        ?User $user,
        array $preparedItems,
        array $quotationTotals,
        string $quotationNumber,
        string $guestSessionId,
    ): Quotation {
        return DB::transaction(function () use ($quotationData, $user, $preparedItems, $quotationTotals, $quotationNumber, $guestSessionId): Quotation {
            // Step 1: save the quotation header.
            $quotation = Quotation::query()->create([
                'quotation_number' => $quotationNumber,
                'requester_type' => $user ? 'user' : 'guest',
                'created_by_user_id' => $user?->id,
                'owner_user_id' => $user?->id,
                'owner_company_id' => $user?->company_id,
                'target_type' => $quotationData['purpose'],
                'target_name' => $quotationData['customer_name'],
                'target_email' => $quotationData['customer_email'],
                'target_phone' => $quotationData['customer_phone'] ?: null,
                'target_company_id' => isset($quotationData['target_company_id']) ? (int) $quotationData['target_company_id'] : null,
                'status' => 'generated',
                'currency' => $quotationTotals['currency'],
                'subtotal' => $quotationTotals['subtotal'],
                'tax_amount' => $quotationTotals['tax_amount'],
                'discount_amount' => $quotationTotals['discount_amount'],
                'price_after_gst' => $quotationTotals['price_after_gst'],
                'total_amount' => $quotationTotals['total_amount'],
                'guest_session_id' => $user ? null : $guestSessionId,
                'notes' => $quotationData['notes'] ?: null,
            ]);

            // Step 2: save all quotation item rows.
            foreach ($preparedItems as $preparedItem) {
                $quotation->items()->create($preparedItem);
            }

            // Step 3: reload the quotation with the relations needed by the PDF.
            return $this->loadQuotationForPdf($quotation->id);
        });
    }

    public function downloadPdf(Quotation $quotation): Response
    {
        // Step 1: render the quotation PDF with the saved quotation record.
        $pdf = Pdf::loadView('invoice.quotation-pdf', [
            'quotation' => $quotation,
        ])->setPaper(
            config('invoice.pdf.paper', 'a4'),
            config('invoice.pdf.orientation', 'portrait'),
        );

        return $pdf->download($quotation->quotation_number.'.pdf');
    }

    protected function loadVisibleProducts(?User $user)
    {
        // Step 1: load the visible products from the current visibility scope.
        $products = $this->dataVisibilityService->visibleProductQuery($user)
            ->orderBy('products.name')
            ->get();

        // Step 2: keep the quotation page usable even when visible pricing is not ready.
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

    protected function loadQuotationForPdf(int $quotationId): Quotation
    {
        return Quotation::query()
            ->with([
                'creator:id,name,email',
                'ownerUser:id,name,email',
                'ownerCompany:id,name,company_type',
                'targetCompany:id,name,company_type',
                'items' => function ($query): void {
                    $query->orderBy('id');
                },
            ])
            ->findOrFail($quotationId);
    }
}
