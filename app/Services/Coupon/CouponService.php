<?php

namespace App\Services\Coupon;

use App\Models\Pricing\Coupon;
use App\Models\Pricing\ProductBulkPrice;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class CouponService
{
    // This validates the entered coupon and returns the cleaned code.
    public function readValidatedCouponCode(?string $couponCode): ?string
    {
        $validatedCouponCode = null;

        try {
            // Step 1: clean the entered coupon code.
            $normalizedCouponCode = $this->normalizeCouponCode($couponCode);

            // Step 2: stop early when no coupon code was entered.
            if ($normalizedCouponCode === null) {
                $validatedCouponCode = null;
            } else {
                // Step 3: load the active coupon from database.
                $activeCoupon = $this->findActiveCouponByCode($normalizedCouponCode);

                // Step 4: stop the flow when the coupon is not active.
                if (! $activeCoupon) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'The selected coupon is invalid or expired.',
                    ]);
                }

                // Step 5: keep the saved code ready for the caller.
                $validatedCouponCode = (string) $activeCoupon->code;
            }
        } catch (Throwable $exception) {
            Log::error('Failed to validate coupon code.', [
                'coupon_code' => $couponCode,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $validatedCouponCode;
    }

    // This finds one active coupon by its business code.
    public function findActiveCouponByCode(?string $couponCode): ?Coupon
    {
        $activeCoupon = null;

        try {
            // Step 1: clean the entered coupon code.
            $normalizedCouponCode = $this->normalizeCouponCode($couponCode);

            // Step 2: stop early when the code is empty.
            if ($normalizedCouponCode === null) {
                $activeCoupon = null;
            } else {
                // Step 3: load the active coupon within the valid date range.
                $activeCoupon = Coupon::query()
                    ->where('code', $normalizedCouponCode)
                    ->where('is_active', true)
                    ->where(function ($builder): void {
                        $builder->whereNull('valid_from')
                            ->orWhere('valid_from', '<=', now());
                    })
                    ->where(function ($builder): void {
                        $builder->whereNull('valid_to')
                            ->orWhere('valid_to', '>=', now());
                    })
                    ->first();
            }
        } catch (Throwable $exception) {
            Log::error('Failed to find active coupon by code.', [
                'coupon_code' => $couponCode,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $activeCoupon;
    }

    // This calculates the coupon discount from the current chargeable amount.
    public function calculateCouponDiscount(float $couponBaseAmount, Coupon $activeCoupon): array
    {
        $couponDiscountDetails = [];

        try {
            // Step 1: read the saved coupon discount type and value.
            $discountType = strtolower(trim((string) $activeCoupon->discount_type));
            $discountValue = round(max(0, (float) $activeCoupon->discount_value), 2);

            // Step 2: calculate the discount amount from the coupon type.
            if ($discountType === 'percent') {
                $discountValue = min($discountValue, 100);
                $discountAmount = round(($couponBaseAmount * $discountValue) / 100, 2);
            } else {
                $discountAmount = $discountValue;
            }

            // Step 3: keep the discount amount within the current chargeable amount.
            $discountAmount = min($discountAmount, $couponBaseAmount);

            // Step 4: prepare the final discount details.
            $couponDiscountDetails = [
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'final_amount' => round($couponBaseAmount - $discountAmount, 2),
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to calculate coupon discount.', [
                'coupon_code' => $activeCoupon->code ?? null,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $couponDiscountDetails;
    }

    // This resolves how the coupon should behave with the current price setup.
    public function resolveCouponPricingDetails(   ?Coupon $activeCoupon,   string $priceType,   float $baseAmount,  ?ProductBulkPrice $matchingBulkPrice, array $productDiscountDetails,
    ): array {
        $couponPricingDetails = [];

        try {
            // Step 1: start with the standard no-coupon state.
            $workingAmount = round($baseAmount, 2);
            $productDiscountAmount = 0.0;
            $bulkDiscountAmount = 0.0;
            $couponDiscountAmount = 0.0;
            $appliedCouponCode = null;
            $pricingStage = $priceType === 'company_price' ? 'company_price' : 'base_price';
            $couponStatus = 'not_provided';
            $couponMessage = null;

            // Step 2: stop early when no coupon was provided.
            if (! $activeCoupon) {
                $couponPricingDetails = [
                    'working_amount' => $workingAmount,
                    'product_discount_amount' => $productDiscountAmount,
                    'bulk_discount_amount' => $bulkDiscountAmount,
                    'coupon_discount_amount' => $couponDiscountAmount,
                    'applied_coupon_code' => $appliedCouponCode,
                    'pricing_stage' => $pricingStage,
                    'coupon_status' => $couponStatus,
                    'coupon_message' => $couponMessage,
                ];
            } else {
                // Step 3: check whether the current item is already on company pricing.
                $isCompanyPrice = $priceType === 'company_price';

                // Step 4: read the matching bulk and product discount states.
                $hasBulkPrice = ! $isCompanyPrice && $matchingBulkPrice !== null;
                $hasProductDiscount = ! $isCompanyPrice && (float) ($productDiscountDetails['discount_amount'] ?? 0) > 0;

                // Step 5: stop when the coupon is not allowed on company pricing.
                if ($isCompanyPrice && ! $activeCoupon->allow_on_company_price) {
                    $couponStatus = 'blocked_company_price';
                    $couponMessage = 'This coupon cannot be applied on company pricing.';
                } else {
                    // Step 6: apply the bulk price first when this coupon allows bulk combination.
                    if ($hasBulkPrice && $activeCoupon->allow_with_bulk) {
                        $workingAmount = round((float) $matchingBulkPrice->amount, 2);
                        $bulkDiscountAmount = round(max(0, $baseAmount - $workingAmount), 2);
                        $pricingStage = 'bulk_price';
                        $couponStatus = 'applied_with_bulk_price';
                        $couponMessage = 'Coupon applied with eligible bulk pricing.';
                    }

                    // Step 7: apply the product discount first when bulk was not used and this coupon allows product discount combination.
                    if ($couponStatus === 'not_provided' && $hasProductDiscount && $activeCoupon->allow_with_product_discount) {
                        $workingAmount = round((float) ($productDiscountDetails['final_amount'] ?? $workingAmount), 2);
                        $productDiscountAmount = round((float) ($productDiscountDetails['discount_amount'] ?? 0), 2);
                        $pricingStage = 'product_discount';
                        $couponStatus = 'applied_with_product_discount';
                        $couponMessage = 'Coupon applied with current product discount.';
                    }

                    // Step 8: prepare a clear message when the coupon will override another pricing benefit instead of stacking with it.
                    if ($couponStatus === 'not_provided') {
                        if ($isCompanyPrice) {
                            $couponStatus = 'applied_on_company_price';
                            $couponMessage = 'Coupon applied on company pricing.';
                        } elseif ($hasBulkPrice) {
                            $couponStatus = 'applied_over_bulk_price';
                            $couponMessage = 'Coupon applied. Bulk pricing is not combined with this coupon.';
                        } elseif ($hasProductDiscount) {
                            $couponStatus = 'applied_over_product_discount';
                            $couponMessage = 'Coupon applied. Product discount is not combined with this coupon.';
                        } else {
                            $couponStatus = 'applied';
                            $couponMessage = 'Coupon applied successfully.';
                        }
                    }

                    // Step 9: calculate the final coupon discount from the current working amount.
                    $couponDiscountDetails = $this->calculateCouponDiscount($workingAmount, $activeCoupon);
                    $couponDiscountAmount = round((float) ($couponDiscountDetails['discount_amount'] ?? 0), 2);
                    $workingAmount = round((float) ($couponDiscountDetails['final_amount'] ?? $workingAmount), 2);
                    $appliedCouponCode = (string) $activeCoupon->code;
                    $pricingStage = 'coupon';
                }

                // Step 10: return the final coupon pricing details.
                $couponPricingDetails = [
                    'working_amount' => $workingAmount,
                    'product_discount_amount' => round($productDiscountAmount, 2),
                    'bulk_discount_amount' => round($bulkDiscountAmount, 2),
                    'coupon_discount_amount' => round($couponDiscountAmount, 2),
                    'applied_coupon_code' => $appliedCouponCode,
                    'pricing_stage' => $pricingStage,
                    'coupon_status' => $couponStatus,
                    'coupon_message' => $couponMessage,
                ];
            }
        } catch (Throwable $exception) {
            Log::error('Failed to resolve coupon pricing details.', [  'coupon_code' => $activeCoupon?->code,  'price_type' => $priceType,    'error' => $exception->getMessage(),  ]);

            throw $exception;
        }

        return $couponPricingDetails;
    }

    // This stops checkout when the coupon does not reduce any prepared item.
    public function ensureCouponAppliesToPreparedItems(?string $couponCode, array $preparedOrderItems, string $notApplicableMessage): void
    {
        try {
            // Step 1: stop early when no coupon code was entered.
            $normalizedCouponCode = $this->normalizeCouponCode($couponCode);

            if ($normalizedCouponCode !== null) {
                // Step 2: calculate the full coupon discount amount across all items.
                $couponDiscountAmount = $this->preparedItemsCouponDiscountAmount($preparedOrderItems);

                // Step 3: stop the flow when the coupon did not affect any item.
                if ($couponDiscountAmount <= 0) {
                    $couponMessage = $this->preparedItemsCouponMessage($preparedOrderItems);

                    if ($couponMessage === null) {
                        $couponMessage = $notApplicableMessage;
                    }

                    throw ValidationException::withMessages([
                        'coupon_code' => $couponMessage,
                    ]);
                }
            }
        } catch (Throwable $exception) {
            Log::error('Failed to confirm coupon item eligibility.', [
                'coupon_code' => $couponCode,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This prepares the coupon preview summary used by the checkout AJAX call.
    public function buildCouponPreview(?string $couponCode, array $preparedOrderItems, string $defaultSuccessMessage, string $defaultErrorMessage): array
    {
        $couponPreview = [];

        try {
            // Step 1: calculate the final summary totals from the prepared items.
            $orderSubtotalAmount = $this->preparedItemsAmount($preparedOrderItems, 'subtotal_amount');
            $orderTaxAmount = $this->preparedItemsAmount($preparedOrderItems, 'tax_amount');
            $orderTotalAmount = $this->preparedItemsAmount($preparedOrderItems, 'total_amount');
            $couponDiscountAmount = $this->preparedItemsCouponDiscountAmount($preparedOrderItems);

            // Step 2: read the best message that the pricing flow prepared for the coupon.
            $couponMessage = $this->preparedItemsCouponMessage($preparedOrderItems);

            // Step 3: use the normal success message when the coupon applied but no custom note was prepared.
            if ($couponDiscountAmount > 0 && $couponMessage === null) {
                $couponMessage = $defaultSuccessMessage;
            }

            // Step 4: use the normal error message when the coupon did not apply and no custom note was prepared.
            if ($couponDiscountAmount <= 0 && $couponMessage === null) {
                $couponMessage = $defaultErrorMessage;
            }

            // Step 5: prepare the final preview payload for the UI.
            $couponPreview = [
                'coupon_code' => $this->normalizeCouponCode($couponCode),
                'is_coupon_applied' => $couponDiscountAmount > 0,
                'coupon_message' => $couponMessage,
                'coupon_discount_amount' => round($couponDiscountAmount, 4),
                'order_subtotal_amount' => round($orderSubtotalAmount, 4),
                'order_tax_amount' => round($orderTaxAmount, 4),
                'order_total_amount' => round($orderTotalAmount, 4),
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build coupon preview.', [
                'coupon_code' => $couponCode,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $couponPreview;
    }

    // This reads one active coupon code for reward and other display-only flows.
    public function readActiveCouponCode(?string $couponCode): ?string
    {
        $activeCouponCode = null;

        try {
            // Step 1: load the active coupon from database.
            $activeCoupon = $this->findActiveCouponByCode($couponCode);

            // Step 2: return the saved code when the coupon is active.
            if ($activeCoupon) {
                $activeCouponCode = (string) $activeCoupon->code;
            }
        } catch (Throwable $exception) {
            Log::error('Failed to read active coupon code.', [
                'coupon_code' => $couponCode,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $activeCouponCode;
    }

    // This converts the entered coupon into one clean uppercase code.
    protected function normalizeCouponCode(?string $couponCode): ?string
    {
        $normalizedCouponCode = strtoupper(trim((string) $couponCode));

        if ($normalizedCouponCode === '') {
            $normalizedCouponCode = null;
        }

        return $normalizedCouponCode;
    }

    // This sums one amount column across the prepared order items.
    protected function preparedItemsAmount(array $preparedOrderItems, string $amountKey): float
    {
        $totalAmount = 0.0;

        // Step 1: add the selected amount from each prepared item.
        foreach ($preparedOrderItems as $preparedOrderItem) {
            $totalAmount += (float) ($preparedOrderItem[$amountKey] ?? 0);
        }

        return round($totalAmount, 4);
    }

    // This calculates the coupon-only discount across prepared items.
    protected function preparedItemsCouponDiscountAmount(array $preparedOrderItems): float
    {
        $couponDiscountAmount = 0.0;

        // Step 1: add the coupon discount from each prepared item.
        foreach ($preparedOrderItems as $preparedOrderItem) {
            $itemSnapshot = $preparedOrderItem['item_snapshot'] ?? [];
            $lineCouponDiscount = (float) ($itemSnapshot['coupon_discount_amount'] ?? 0);
            $itemQuantity = (int) ($preparedOrderItem['quantity'] ?? 0);
            $couponDiscountAmount += $lineCouponDiscount * $itemQuantity;
        }

        return round($couponDiscountAmount, 4);
    }

    // This reads one readable coupon message from the prepared items.
    protected function preparedItemsCouponMessage(array $preparedOrderItems): ?string
    {
        $couponMessage = null;

        // Step 1: prefer the message from an item where the coupon actually applied.
        foreach ($preparedOrderItems as $preparedOrderItem) {
            $itemSnapshot = $preparedOrderItem['item_snapshot'] ?? [];
            $lineCouponDiscount = (float) ($itemSnapshot['coupon_discount_amount'] ?? 0);
            $preparedMessage = trim((string) ($itemSnapshot['coupon_message'] ?? ''));

            if ($lineCouponDiscount > 0 && $preparedMessage !== '') {
                $couponMessage = $preparedMessage;
                break;
            }
        }

        // Step 2: fall back to the first available coupon message when no applied item message exists.
        if ($couponMessage === null) {
            foreach ($preparedOrderItems as $preparedOrderItem) {
                $itemSnapshot = $preparedOrderItem['item_snapshot'] ?? [];
                $preparedMessage = trim((string) ($itemSnapshot['coupon_message'] ?? ''));

                if ($preparedMessage !== '') {
                    $couponMessage = $preparedMessage;
                    break;
                }
            }
        }

        return $couponMessage;
    }
}
