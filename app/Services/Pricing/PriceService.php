<?php

namespace App\Services\Pricing;

use App\Models\Authorization\User;
use App\Models\Pricing\Coupon;
use App\Models\Pricing\ProductBulkPrice;
use App\Models\Product\ProductPrice;
use App\Models\Product\ProductVariant;
use App\Services\Authorization\RolePermissionService;
use App\Services\Coupon\CouponService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class PriceService
{
    public function __construct(
        protected RolePermissionService $rolePermissionService,
        protected CouponService $couponService,
    ) {
    }

    // This returns the visible price order from most-specific to least-specific for the current shopper.
    public function visiblePriceTypes(?User $user): array
    {
        try {
            // Business rule: guests can only see public-style pricing.
            if (! $user) {
                return ['retail', 'public'];
            }

            // Business rule: admin-capable users can inspect every standard price ladder.
            if ($this->isAdminCapable($user)) {
                return ['company_price', 'logged_in', 'dealer', 'institutional', 'retail', 'public'];
            }

            // Business rule: B2C users first see logged-in consumer pricing.
            if ($user->isB2c()) {
                return ['logged_in', 'retail', 'public'];
            }

            // Business rule: B2B subtype decides the default commercial price ladder.
            if ($user->isB2b()) {
                if (in_array($user->b2b_type, ['dealer', 'distributor'], true)) {
                    return ['company_price', 'dealer', 'logged_in', 'public', 'retail'];
                }

                if (in_array($user->b2b_type, ['lab', 'hospital'], true)) {
                    return ['company_price', 'institutional', 'logged_in', 'public', 'retail'];
                }

                return ['company_price', 'dealer', 'institutional', 'logged_in', 'public', 'retail'];
            }

            return ['retail', 'public'];
        } catch (Throwable $exception) {
            Log::error('Failed to resolve visible price types.', ['user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This resolves the first purchasable visible price for a full product.
    public function resolveProductPrice(int $productId, ?User $user, int $quantity = 1, ?string $couponCode = null): ?array
    {
        try {
            // Business rule: walk variants in their natural order so the same default variant is chosen consistently.
            $variantIds = ProductVariant::query()
                ->where('product_id', $productId)
                ->where('is_active', true)
                ->orderBy('id')
                ->pluck('id');

            foreach ($variantIds as $variantId) {
                $resolvedPrice = $this->resolveVariantPrice((int) $variantId, $user, $quantity, $couponCode);

                if ($resolvedPrice) {
                    return $resolvedPrice;
                }
            }

            return null;
        } catch (Throwable $exception) {
            Log::error('Failed to resolve product price.', ['product_id' => $productId, 'user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This resolves the final purchase price for one exact sellable variant.
    public function resolveVariantPrice(int $productVariantId, ?User $user, int $quantity = 1, ?string $couponCode = null): ?array
    {
        try {
            // Business step: load the selected variant with all active price rows because the final purchase price depends on both variant rules and price ladders.
            $variant = ProductVariant::query()
                ->with([
                    'prices' => fn ($builder) => $builder->where('is_active', true)->orderBy('id'),
                ])
                ->whereKey($productVariantId)
                ->where('is_active', true)
                ->first();

            if (! $variant) {
                return null;
            }

            // Business step: select the base commercial price row that this shopper is allowed to use.
            $selectedPriceRow = $this->selectBasePriceRow($variant, $user);

            if (! $selectedPriceRow) {
                return null;
            }

            // Business step: keep quantity safe and predictable before pricing rules are applied.
            $purchaseQuantity = max(1, $quantity);

            // Business step: read the correct order limits for the shopper type from the variant itself.
            $quantityRules = $this->resolveQuantityRules($variant, $user);

            // Business step: look for a matching bulk price only when the current quantity qualifies for one.
            $matchingBulkPrice = $this->findMatchingBulkPriceRow($variant, $user, $purchaseQuantity);

            // Business step: load the coupon only when the caller provided one.
            $selectedCoupon = $this->couponService->findActiveCouponByCode($couponCode);

            // Business step: calculate the final unit price and all pricing breakdown fields in one place.
            return $this->buildResolvedPricePayload(
                $variant,
                $selectedPriceRow,
                $quantityRules,
                $matchingBulkPrice,
                $selectedCoupon,
            );
        } catch (Throwable $exception) {
            Log::error('Failed to resolve variant price.', ['product_variant_id' => $productVariantId, 'user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This returns the visible bulk tiers that the current shopper can use for one variant.
    public function listBulkPriceTiers(int $productVariantId, ?User $user): Collection
    {
        try {
            // Business step: load the current visible base price first because every tier is compared against that standard price.
            $baseResolvedPrice = $this->resolveVariantPrice($productVariantId, $user);

            if (! $baseResolvedPrice) {
                return collect();
            }

            // Business step: read the raw bulk slabs visible to this shopper and keep only the best slab for each quantity range.
            $bulkPriceRows = $this->visibleBulkPriceRows($productVariantId, $user)
                ->sortBy([
                    ['min_quantity', 'asc'],
                    ['max_quantity', 'asc'],
                ])
                ->groupBy(fn (ProductBulkPrice $bulkPrice) => $bulkPrice->min_quantity.'-'.($bulkPrice->max_quantity ?? 'open'))
                ->map(fn (Collection $groupedRows) => $groupedRows->sortByDesc(fn (ProductBulkPrice $bulkPrice) => $this->scopePriorityScore($bulkPrice, $user))->first())
                ->filter()
                ->values();

            if ($bulkPriceRows->isEmpty()) {
                return collect([
                    $this->buildDisplayTierRow(
                        minQuantity: $baseResolvedPrice['min_order_quantity'] ?? 1,
                        maxQuantity: $baseResolvedPrice['max_order_quantity'] ?? null,
                        unitAmount: (float) ($baseResolvedPrice['amount'] ?? 0),
                        baseAmount: (float) ($baseResolvedPrice['base_amount'] ?? 0),
                        label: 'Standard Price',
                    ),
                ]);
            }

            // Business step: build a first row for the standard quantity band before bulk slabs start.
            $firstBulkRow = $bulkPriceRows->first();
            $standardMinQuantity = max(1, (int) ($baseResolvedPrice['min_order_quantity'] ?? 1));
            $standardMaxQuantity = max($standardMinQuantity, ((int) $firstBulkRow->min_quantity) - 1);

            $tierRows = collect([
                $this->buildDisplayTierRow(
                    minQuantity: $standardMinQuantity,
                    maxQuantity: $standardMaxQuantity,
                    unitAmount: (float) ($baseResolvedPrice['amount'] ?? 0),
                    baseAmount: (float) ($baseResolvedPrice['base_amount'] ?? 0),
                    label: $this->buildTierLabel($standardMinQuantity, $standardMaxQuantity),
                ),
            ]);

            // Business step: add the visible bulk slabs after the standard row so the product page can render a full pricing ladder.
            foreach ($bulkPriceRows as $bulkPriceRow) {
                $tierRows->push(
                    $this->buildDisplayTierRow(
                        minQuantity: (int) $bulkPriceRow->min_quantity,
                        maxQuantity: $bulkPriceRow->max_quantity === null ? null : (int) $bulkPriceRow->max_quantity,
                        unitAmount: (float) $bulkPriceRow->amount,
                        baseAmount: (float) ($baseResolvedPrice['base_amount'] ?? 0),
                        label: $this->buildTierLabel((int) $bulkPriceRow->min_quantity, $bulkPriceRow->max_quantity === null ? null : (int) $bulkPriceRow->max_quantity),
                    ),
                );
            }

            return $tierRows->values();
        } catch (Throwable $exception) {
            Log::error('Failed to list bulk price tiers.', ['product_variant_id' => $productVariantId, 'user_id' => $user?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This selects the one base price row that the current shopper is allowed to buy on.
    protected function selectBasePriceRow(ProductVariant $variant, ?User $user): ?ProductPrice
    {
        // Business step: company-specific prices override all generic ladders for the same visible variant.
        if ($user && $user->isB2b() && $user->company_id) {
            $companyPrice = $variant->prices
                ->first(fn (ProductPrice $price) => $price->price_type === 'company_price' && (int) $price->company_id === (int) $user->company_id);

            if ($companyPrice) {
                return $companyPrice;
            }
        }

        // Business step: when no company contract price exists, use the normal user-type price ladder in order.
        foreach (array_values(array_filter($this->visiblePriceTypes($user), fn (string $type) => $type !== 'company_price')) as $priceType) {
            $matchedPrice = $variant->prices
                ->first(fn (ProductPrice $price) => $price->price_type === $priceType && $price->company_id === null);

            if ($matchedPrice) {
                return $matchedPrice;
            }
        }

        return null;
    }

    // This reads the correct quantity rules from the sellable variant based on the shopper type.
    protected function resolveQuantityRules(ProductVariant $variant, ?User $user): array
    {
        // Business step: B2B buyers can have different quantity limits than B2C or guest buyers.
        $minimumQuantity = $user && $user->isB2b()
            ? ($variant->b2b_min_order_quantity ?? $variant->min_order_quantity ?? 1)
            : ($variant->b2c_min_order_quantity ?? $variant->min_order_quantity ?? 1);

        $maximumQuantity = $user && $user->isB2b()
            ? ($variant->b2b_max_order_quantity ?? $variant->max_order_quantity)
            : ($variant->b2c_max_order_quantity ?? $variant->max_order_quantity);

        return [
            'min_order_quantity' => max(1, (int) $minimumQuantity),
            'max_order_quantity' => $maximumQuantity === null ? null : (int) $maximumQuantity,
            'lot_size' => max(1, (int) ($variant->lot_size ?? 1)),
        ];
    }

    // This finds the best matching bulk price row for the current shopper and quantity.
    protected function findMatchingBulkPriceRow(ProductVariant $variant, ?User $user, int $quantity): ?ProductBulkPrice
    {
        return $this->visibleBulkPriceRows((int) $variant->id, $user)
            ->filter(function (ProductBulkPrice $bulkPrice) use ($quantity): bool {
                if ($quantity < (int) $bulkPrice->min_quantity) {
                    return false;
                }

                if ($bulkPrice->max_quantity !== null && $quantity > (int) $bulkPrice->max_quantity) {
                    return false;
                }

                return true;
            })
            ->sortByDesc(fn (ProductBulkPrice $bulkPrice) => ($this->scopePriorityScore($bulkPrice, $user) * 100000) + (int) $bulkPrice->min_quantity)
            ->first();
    }

    // This loads only the bulk slabs that are visible to the current shopper.
    protected function visibleBulkPriceRows(int $productVariantId, ?User $user): Collection
    {
        $visibleBulkPrices = ProductBulkPrice::query()
            ->where('product_variant_id', $productVariantId)
            ->where('is_active', true)
            ->orderBy('min_quantity')
            ->orderBy('id')
            ->get();

        $currentRoleIds = $this->currentRoleIds($user);

        return $visibleBulkPrices
            ->filter(function (ProductBulkPrice $bulkPrice) use ($user, $currentRoleIds): bool {
                // Business step: a user-specific slab is only visible to that exact shopper.
                if ($bulkPrice->user_id !== null) {
                    return $user && (int) $bulkPrice->user_id === (int) $user->id;
                }

                // Business step: a role-specific slab is visible only when the shopper currently carries that role.
                if ($bulkPrice->role_id !== null) {
                    return in_array((int) $bulkPrice->role_id, $currentRoleIds, true);
                }

                // Business step: a user-type slab is visible only when the current shopper type matches.
                if ($bulkPrice->applies_to_user_type !== null) {
                    $currentUserType = $user?->user_type ?? 'guest';

                    return trim((string) $bulkPrice->applies_to_user_type) === $currentUserType;
                }

                // Business step: a fully open slab is visible to everyone.
                return true;
            })
            ->values();
    }

    // This returns role ids for the current shopper after default role assignment rules are applied.
    protected function currentRoleIds(?User $user): array
    {
        if (! $user) {
            return [];
        }

        // Business step: reusing the existing role resolver keeps pricing aligned with the live RBAC setup.
        $this->rolePermissionService->roleSlugsForUser($user);

        return $user->fresh()
            ->roles()
            ->pluck('roles.id')
            ->map(fn ($roleId) => (int) $roleId)
            ->all();
    }

    // This gives a numeric priority so the most specific pricing rule wins when ranges overlap.
    protected function scopePriorityScore(ProductBulkPrice $bulkPrice, ?User $user): int
    {
        if ($user && $bulkPrice->user_id !== null && (int) $bulkPrice->user_id === (int) $user->id) {
            return 400;
        }

        if ($bulkPrice->role_id !== null) {
            return 300;
        }

        if ($bulkPrice->applies_to_user_type !== null) {
            return 200;
        }

        return 100;
    }

    // This converts the selected price row plus optional bulk and coupon rules into the final purchase payload.
    protected function buildResolvedPricePayload(
        ProductVariant $variant,
        ProductPrice $selectedPriceRow,
        array $quantityRules,
        ?ProductBulkPrice $matchingBulkPrice,
        ?Coupon $selectedCoupon,
    ): array {
        // Business step: start from the saved price row amount before any runtime pricing rule changes it.
        $baseAmount = round((float) $selectedPriceRow->amount, 2);

        // Business step: company prices are already negotiated final commercial prices, so product discounts should not change them.
        $productDiscountDetails = $selectedPriceRow->price_type === 'company_price'
            ? $this->emptyDiscountDetails($baseAmount)
            : $this->calculateConfiguredDiscount($baseAmount, $selectedPriceRow->DiscountType, $selectedPriceRow->Discount);

        // Business step: let the shared coupon service decide how coupon rules behave with bulk, product discount, and company price.
        $couponPricingDetails = $this->couponService->resolveCouponPricingDetails(
            $selectedCoupon,
            (string) $selectedPriceRow->price_type,
            $baseAmount,
            $matchingBulkPrice,
            $productDiscountDetails,
        );

        $workingAmount = round((float) ($couponPricingDetails['working_amount'] ?? $baseAmount), 2);
        $productDiscountAmount = round((float) ($couponPricingDetails['product_discount_amount'] ?? 0), 2);
        $bulkDiscountAmount = round((float) ($couponPricingDetails['bulk_discount_amount'] ?? 0), 2);
        $couponDiscountAmount = round((float) ($couponPricingDetails['coupon_discount_amount'] ?? 0), 2);
        $appliedCouponCode = $couponPricingDetails['applied_coupon_code'] ?? null;
        $pricingStage = $couponPricingDetails['pricing_stage'] ?? ($selectedPriceRow->price_type === 'company_price' ? 'company_price' : 'base_price');
        $couponStatus = $couponPricingDetails['coupon_status'] ?? 'not_provided';
        $couponMessage = $couponPricingDetails['coupon_message'] ?? null;

        // Business step: when no coupon changed the price, bulk price replaces the base price for the matching quantity slab.
        if ($selectedPriceRow->price_type !== 'company_price' && $appliedCouponCode === null && $matchingBulkPrice) {
            $workingAmount = round((float) $matchingBulkPrice->amount, 2);
            $bulkDiscountAmount = round(max(0, $baseAmount - $workingAmount), 2);
            $pricingStage = 'bulk_price';
        }

        // Business step: when neither coupon nor bulk is active, the configured product discount can reduce the standard price row.
        if ($selectedPriceRow->price_type !== 'company_price' && $appliedCouponCode === null && ! $matchingBulkPrice && $productDiscountDetails['discount_amount'] > 0) {
            $workingAmount = $productDiscountDetails['final_amount'];
            $productDiscountAmount = $productDiscountDetails['discount_amount'];
            $pricingStage = 'product_discount';
        }

        // Business step: GST must be recalculated from the final unit amount after every pricing rule is applied.
        $gstRate = round((float) $selectedPriceRow->gst_rate, 2);
        $taxAmount = round(($workingAmount * $gstRate) / 100, 2);
        $priceAfterGst = round($workingAmount + $taxAmount, 2);
        $totalDiscountAmount = round($productDiscountAmount + $bulkDiscountAmount + $couponDiscountAmount, 2);

        return [
            'base_amount' => $baseAmount,
            'amount' => round($workingAmount, 2),
            'gst_rate' => $gstRate,
            'tax_amount' => $taxAmount,
            'price_after_gst' => $priceAfterGst,
            'currency' => (string) $selectedPriceRow->currency,
            'discount_type' => $productDiscountDetails['discount_type'],
            'discount_value' => $productDiscountDetails['discount_value'],
            'discount_amount' => $totalDiscountAmount,
            'product_discount_amount' => round($productDiscountAmount, 2),
            'bulk_discount_amount' => round($bulkDiscountAmount, 2),
            'coupon_discount_amount' => round($couponDiscountAmount, 2),
            'applied_coupon_code' => $appliedCouponCode,
            'pricing_stage' => $pricingStage,
            'coupon_status' => $couponStatus,
            'coupon_message' => $couponMessage,
            'min_order_quantity' => $quantityRules['min_order_quantity'],
            'max_order_quantity' => $quantityRules['max_order_quantity'],
            'lot_size' => $quantityRules['lot_size'],
            'price_type' => (string) $selectedPriceRow->price_type,
            'product_variant_id' => (int) $variant->id,
            'variant_sku' => (string) $variant->sku,
            'variant_name' => (string) $variant->variant_name,
        ];
    }

    // This calculates the configured product discount from the saved price row.
    protected function calculateConfiguredDiscount(float $baseAmount, mixed $discountType, mixed $discountValue): array
    {
        $finalDiscountType = strtolower(trim((string) $discountType));

        if (! in_array($finalDiscountType, ['cash', 'percent'], true)) {
            $finalDiscountType = 'cash';
        }

        $finalDiscountValue = round(max(0, (float) $discountValue), 2);

        if ($finalDiscountType === 'percent') {
            $finalDiscountValue = min($finalDiscountValue, 100);
            $discountAmount = round(($baseAmount * $finalDiscountValue) / 100, 2);
        } else {
            $discountAmount = $finalDiscountValue;
        }

        $discountAmount = min($discountAmount, $baseAmount);

        return [
            'discount_type' => $finalDiscountType,
            'discount_value' => $finalDiscountValue,
            'discount_amount' => $discountAmount,
            'final_amount' => round($baseAmount - $discountAmount, 2),
        ];
    }

    // This returns a no-discount structure when a lower-priority discount should not run.
    protected function emptyDiscountDetails(float $baseAmount): array
    {
        return [
            'discount_type' => 'cash',
            'discount_value' => 0.0,
            'discount_amount' => 0.0,
            'final_amount' => round($baseAmount, 2),
        ];
    }

    // This builds one clean tier row used by the product detail pricing ladder.
    protected function buildDisplayTierRow(int $minQuantity, ?int $maxQuantity, float $unitAmount, float $baseAmount, string $label): array
    {
        $discountAmount = round(max(0, $baseAmount - $unitAmount), 2);
        $discountPercent = $baseAmount > 0 ? round(($discountAmount / $baseAmount) * 100, 2) : 0;

        return [
            'label' => $label,
            'discount' => $discountPercent > 0 ? rtrim(rtrim(number_format($discountPercent, 2, '.', ''), '0'), '.').'% Off' : 'Current Price',
            'price' => round($unitAmount, 2),
            'min' => $minQuantity,
            'max' => $maxQuantity,
            'discount_value' => $discountPercent,
        ];
    }

    // This converts quantity bounds into a human-readable tier label for the storefront.
    protected function buildTierLabel(int $minQuantity, ?int $maxQuantity): string
    {
        if ($maxQuantity === null) {
            return $minQuantity.'+ Units';
        }

        if ($minQuantity === $maxQuantity) {
            return $minQuantity.' Unit';
        }

        return $minQuantity.' - '.$maxQuantity.' Units';
    }

    // This checks whether the current user can act with admin-level pricing visibility.
    protected function isAdminCapable(User $user): bool
    {
        return $this->rolePermissionService->hasRole($user, 'admin')
            || $this->rolePermissionService->hasRole($user, 'delegated_admin')
            || in_array($user->user_type, ['admin', 'delegated_admin'], true);
    }
}
