<?php

namespace App\Services\Checkout;

use App\Models\Authorization\User;
use App\Models\Authorization\UserAddress;
use App\Models\Cart\Cart;
use App\Models\Order\Order;
use App\Services\Cart\CartService;
use App\Services\Coupon\CouponService;
use App\Services\Notification\EmailNotificationService;
use App\Services\Pricing\PriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected PriceService $priceService,
        protected CouponService $couponService,
        protected EmailNotificationService $emailNotificationService,
    ) {
    }

    // This loads all data needed by the existing checkout page.
    public function loadCheckoutPageData(Request $request): array
    {
        try {
            // Step 1: load the current cart for the active shopper.
            $initialCart = $this->cartService->showCurrentCart($request);

            // Step 2: load the saved addresses for signed-in users only.
            $savedAddresses = $this->loadSavedUserAddressesForCheckout($request->user());

            // Step 3: load the saved invoice details for B2B users only.
            $checkoutBusinessDetails = $this->loadUserBusinessInvoiceDetailsForCheckout($request->user());

            // Step 4: return the full checkout page data.
            return [
                'initialCart' => $initialCart,
                'savedAddresses' => $savedAddresses,
                'checkoutBusinessDetails' => $checkoutBusinessDetails,
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to load checkout page data.', [
                'user_id' => $request->user()?->id,
                'session_id' => $request->session()->get('guest_cart_session_id'),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This creates one submitted order from the current signed-in cart.
    public function placeOrderFromCart(array $validatedCheckout, User $user): array
    {
        try {
            // Step 1: load the current user cart with all cart items.
            $cart = $this->cartService->findUserCart($user);

            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is empty.',
                ]);
            }

            // Step 2: validate the coupon before order creation starts.
            $couponCode = $this->couponService->readValidatedCouponCode($validatedCheckout['coupon_code'] ?? null);

            // Step 3: prepare the final order items using live prices.
            $preparedOrderItems = $this->prepareCheckoutOrderItems($cart, $user, $couponCode);

            // Step 4: stop checkout when the coupon does not apply to the current cart.
            $this->couponService->ensureCouponAppliesToPreparedItems(
                $couponCode,
                $preparedOrderItems,
                'The selected coupon does not apply to the current cart.',
            );

            // Step 5: calculate the final order totals.
            $orderTotals = $this->calculateCheckoutTotals($validatedCheckout, $preparedOrderItems);

            // Step 6: create the order and clear the cart inside one transaction.
            DB::beginTransaction();

            $order = Order::query()->create([
                'placed_by_user_id' => $user->id,
                'company_id' => $user->company_id,
                'status' => 'submitted',
                'currency' => $orderTotals['currency'],
                'subtotal_amount' => $orderTotals['subtotal_amount'],
                'tax_amount' => $orderTotals['tax_amount'],
                'discount_amount' => $orderTotals['discount_amount'],
                'shipping_amount' => $orderTotals['shipping_amount'],
                'adjustment_amount' => $orderTotals['adjustment_amount'],
                'rounding_amount' => $orderTotals['rounding_amount'],
                'total_amount' => $orderTotals['total_amount'],
                'pricing_snapshot' => $orderTotals['pricing_snapshot'],
                'notes' => $validatedCheckout['notes'] ?? null,
                'submitted_at' => now(),
                'cancelled_at' => null,
            ]);

            $createdOrderItems = [];

            foreach ($preparedOrderItems as $preparedOrderItem) {
                $createdOrderItems[] = $order->items()->create($preparedOrderItem);
            }

            // Step 7: store the final discount details on the order items.
            $this->storeCheckoutDiscountLines($order, $createdOrderItems, $preparedOrderItems);

            // Step 8: store the shipping and billing addresses on the order.
            $this->storeCheckoutOrderAddresses($order, $user, $validatedCheckout);

            // Step 9: clear the cart after the order is safely created.
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            // Step 10: send the order confirmation email after commit.
            $this->sendOrderSubmittedEmail($user, $order);

            // Step 11: build a simple order response for the existing UI flow.
            $orderItems = [];

            foreach ($createdOrderItems as $createdOrderItem) {
                $orderItems[] = [
                    'id' => $createdOrderItem->id,
                    'product_name' => $createdOrderItem->product_name,
                    'variant_name' => $createdOrderItem->variant_name,
                    'sku' => $createdOrderItem->sku,
                    'quantity' => (int) $createdOrderItem->quantity,
                    'unit_price' => (float) $createdOrderItem->unit_price,
                    'tax_amount' => (float) $createdOrderItem->tax_amount,
                    'total_amount' => (float) $createdOrderItem->total_amount,
                ];
            }

            return [
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'currency' => $order->currency,
                    'subtotal_amount' => (float) $order->subtotal_amount,
                    'tax_amount' => (float) $order->tax_amount,
                    'discount_amount' => (float) $order->discount_amount,
                    'shipping_amount' => (float) $order->shipping_amount,
                    'adjustment_amount' => (float) $order->adjustment_amount,
                    'rounding_amount' => (float) $order->rounding_amount,
                    'total_amount' => (float) $order->total_amount,
                    'notes' => $order->notes,
                    'items_count' => count($orderItems),
                    'items' => $orderItems,
                ],
            ];
        } catch (Throwable $exception) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Failed to checkout cart.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This prepares the live coupon preview used by the checkout AJAX call.
    public function previewCheckoutCoupon(array $validatedCouponInput, User $user): array
    {
        $couponPreview = [];

        try {
            // Step 1: load the current user cart with its latest items.
            $cart = $this->cartService->findUserCart($user);

            // Step 2: stop the flow when no cart items are available.
            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'coupon_code' => 'Add at least one cart item before applying a coupon.',
                ]);
            }

            // Step 3: validate the entered coupon code.
            $couponCode = $this->couponService->readValidatedCouponCode($validatedCouponInput['coupon_code'] ?? null);

            // Step 4: prepare the current cart items using live coupon-aware pricing.
            $preparedOrderItems = $this->prepareCheckoutOrderItems($cart, $user, $couponCode);

            // Step 5: stop the flow when the coupon does not affect any prepared item.
            $this->couponService->ensureCouponAppliesToPreparedItems(
                $couponCode,
                $preparedOrderItems,
                'The selected coupon does not apply to the current cart.',
            );

            // Step 6: prepare the final preview summary for the checkout UI.
            $couponPreview = $this->couponService->buildCouponPreview(
                $couponCode,
                $preparedOrderItems,
                'Coupon applied successfully.',
                'The selected coupon does not apply to the current cart.',
            );
        } catch (Throwable $exception) {
            Log::error('Failed to preview checkout coupon.', [
                'user_id' => $user->id,
                'coupon_code' => $validatedCouponInput['coupon_code'] ?? null,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $couponPreview;
    }

    // This loads saved user addresses for the checkout address selector.
    protected function loadSavedUserAddressesForCheckout(?User $user)
    {
        // Step 1: keep the address list empty for guests.
        if (! $user) {
            return collect();
        }

        // Step 2: load default shipping first so checkout opens with the most useful address.
        return $user->addresses()
            ->orderByDesc('is_default_shipping')
            ->orderByDesc('is_default_billing')
            ->orderByDesc('id')
            ->get();
    }

    // This prepares GST and business invoice details for the checkout page.
    protected function loadUserBusinessInvoiceDetailsForCheckout(?User $user): array
    {
        // Step 1: keep the invoice details hidden for guests and B2C users.
        if (! $user || ! $user->isB2b()) {
            return [
                'show_business_fields' => false,
                'gstin' => null,
                'pan_number' => null,
                'registered_business_name' => null,
            ];
        }

        // Step 2: load the linked company for saved B2B details.
        $company = $user->company;

        // Step 3: return the saved B2B values for checkout.
        return [
            'show_business_fields' => true,
            'gstin' => $company?->gst_number,
            'pan_number' => $company?->pan_number,
            'registered_business_name' => $company?->legal_name ?: $company?->name,
        ];
    }

    // This prepares final order item rows from the current cart during checkout.
    protected function prepareCheckoutOrderItems(Cart $cart, User $user, ?string $couponCode = null): array
    {
        // Step 1: prepare an array that will hold all final order item rows.
        $preparedOrderItems = [];

        // Step 2: convert each cart item into one final order item payload.
        foreach ($cart->items as $index => $cartItem) {
            $productVariant = $cartItem->variant;
            $product = $productVariant?->product;

            if (! $productVariant || ! $product) {
                throw ValidationException::withMessages([
                    'cart' => 'One cart item is no longer available.',
                ]);
            }

            $quantity = (int) $cartItem->quantity;
            $resolvedVariantPrice = $this->priceService->resolveVariantPrice(
                (int) $cartItem->product_variant_id,
                $user,
                $quantity,
                $couponCode
            );

            if (! $resolvedVariantPrice) {
                throw ValidationException::withMessages([
                    'cart' => 'One cart item is no longer available for checkout.',
                ]);
            }

            // Step 3: confirm the latest quantity rules before creating the order item.
            $this->cartService->validateUserCartQuantity($quantity, $resolvedVariantPrice);

            // Step 4: calculate the final amount values for this order item.
            $unitPrice = round((float) ($resolvedVariantPrice['amount'] ?? 0), 4);
            $unitBasePrice = round((float) ($resolvedVariantPrice['base_amount'] ?? $unitPrice), 4);
            $unitTaxAmount = round((float) ($resolvedVariantPrice['tax_amount'] ?? 0), 4);
            $unitPriceAfterGst = round((float) ($resolvedVariantPrice['price_after_gst'] ?? 0), 4);
            $subtotalAmount = round($unitPrice * $quantity, 4);
            $taxAmount = round($unitTaxAmount * $quantity, 4);
            $totalAmount = round($unitPriceAfterGst * $quantity, 4);
            $lineDiscountAmount = round((float) ($resolvedVariantPrice['discount_amount'] ?? 0) * $quantity, 4);

            // Step 5: add the prepared order item row.
            $preparedOrderItems[] = [
                'product_id' => $product->id,
                'product_variant_id' => $productVariant->id,
                'sku' => $productVariant->sku,
                'product_name' => $product->name,
                'variant_name' => $productVariant->variant_name,
                'description' => 'Created from cart checkout.',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal_amount' => $subtotalAmount,
                'discount_amount' => $lineDiscountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'sort_order' => $index,
                'item_snapshot' => [
                    'source' => 'cart_checkout',
                    'currency' => $resolvedVariantPrice['currency'] ?? 'INR',
                    'price_type' => $resolvedVariantPrice['price_type'] ?? null,
                    'base_unit_price' => $unitBasePrice,
                    'pricing_stage' => $resolvedVariantPrice['pricing_stage'] ?? 'base_price',
                    'gst_rate' => round((float) ($resolvedVariantPrice['gst_rate'] ?? 0), 4),
                    'unit_tax_amount' => $unitTaxAmount,
                    'unit_price_after_gst' => $unitPriceAfterGst,
                    'unit_discount_amount' => round((float) ($resolvedVariantPrice['discount_amount'] ?? 0), 4),
                    'product_discount_amount' => round((float) ($resolvedVariantPrice['product_discount_amount'] ?? 0), 4),
                    'bulk_discount_amount' => round((float) ($resolvedVariantPrice['bulk_discount_amount'] ?? 0), 4),
                    'coupon_discount_amount' => round((float) ($resolvedVariantPrice['coupon_discount_amount'] ?? 0), 4),
                    'applied_coupon_code' => $resolvedVariantPrice['applied_coupon_code'] ?? null,
                    'coupon_status' => $resolvedVariantPrice['coupon_status'] ?? null,
                    'coupon_message' => $resolvedVariantPrice['coupon_message'] ?? null,
                    'min_order_quantity' => (int) ($resolvedVariantPrice['min_order_quantity'] ?? 1),
                    'max_order_quantity' => $resolvedVariantPrice['max_order_quantity'] === null ? null : (int) $resolvedVariantPrice['max_order_quantity'],
                    'lot_size' => (int) ($resolvedVariantPrice['lot_size'] ?? 1),
                    'variant_sku' => $productVariant->sku,
                    'variant_name' => $productVariant->variant_name,
                ],
            ];
        }

        // Step 6: return all final prepared order item rows.
        return $preparedOrderItems;
    }

    // This calculates final order totals during cart checkout.
    protected function calculateCheckoutTotals(array $validatedCheckout, array $preparedOrderItems): array
    {
        // Step 1: read the optional extra amounts passed during checkout.
        $shippingAmount = round((float) ($validatedCheckout['shipping_amount'] ?? 0), 4);
        $adjustmentAmount = round((float) ($validatedCheckout['adjustment_amount'] ?? 0), 4);
        $roundingAmount = round((float) ($validatedCheckout['rounding_amount'] ?? 0), 4);
        $currency = (string) ($preparedOrderItems[0]['item_snapshot']['currency'] ?? 'INR');

        // Step 2: sum all order item amounts into final order totals.
        $subtotalAmount = 0;
        $taxAmount = 0;
        $discountAmount = 0;
        $itemsTotal = 0;
        $priceTypes = [];
        $pricingStages = [];

        foreach ($preparedOrderItems as $preparedOrderItem) {
            $itemSnapshot = $preparedOrderItem['item_snapshot'] ?? [];

            $subtotalAmount += (float) ($preparedOrderItem['subtotal_amount'] ?? 0);
            $taxAmount += (float) ($preparedOrderItem['tax_amount'] ?? 0);
            $discountAmount += (float) ($preparedOrderItem['discount_amount'] ?? 0);
            $itemsTotal += (float) ($preparedOrderItem['total_amount'] ?? 0);

            $priceType = $itemSnapshot['price_type'] ?? null;
            $pricingStage = $itemSnapshot['pricing_stage'] ?? null;

            if ($priceType && ! in_array($priceType, $priceTypes, true)) {
                $priceTypes[] = $priceType;
            }

            if ($pricingStage && ! in_array($pricingStage, $pricingStages, true)) {
                $pricingStages[] = $pricingStage;
            }
        }

        $subtotalAmount = round($subtotalAmount, 4);
        $taxAmount = round($taxAmount, 4);
        $discountAmount = round($discountAmount, 4);
        $itemsTotal = round($itemsTotal, 4);
        $totalAmount = round($itemsTotal + $shippingAmount + $adjustmentAmount + $roundingAmount, 4);

        // Step 3: keep a readable pricing snapshot on the order header.
        $pricingSnapshot = [
            'source' => 'cart_checkout',
            'currency' => $currency,
            'items_count' => count($preparedOrderItems),
            'coupon_code' => $validatedCheckout['coupon_code'] ?? null,
            'subtotal_amount' => $subtotalAmount,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'items_total' => $itemsTotal,
            'shipping_amount' => $shippingAmount,
            'adjustment_amount' => $adjustmentAmount,
            'rounding_amount' => $roundingAmount,
            'total_amount' => $totalAmount,
            'price_types' => $priceTypes,
            'pricing_stages' => $pricingStages,
        ];

        // Step 4: return the final order totals array.
        return [
            'currency' => $currency,
            'subtotal_amount' => $subtotalAmount,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'shipping_amount' => $shippingAmount,
            'adjustment_amount' => $adjustmentAmount,
            'rounding_amount' => $roundingAmount,
            'total_amount' => $totalAmount,
            'pricing_snapshot' => $pricingSnapshot,
        ];
    }

    // This stores the item-level discount reasons created during checkout.
    protected function storeCheckoutDiscountLines(Order $order, array $createdOrderItems, array $preparedOrderItems): void
    {
        // Step 1: stop early when there are no created items to map against.
        if ($createdOrderItems === []) {
            return;
        }

        $discountRows = [];

        // Step 2: build one readable discount row per applied pricing rule on each order item.
        foreach ($createdOrderItems as $index => $createdOrderItem) {
            $itemSnapshot = $preparedOrderItems[$index]['item_snapshot'] ?? [];

            if (($itemSnapshot['product_discount_amount'] ?? 0) > 0) {
                $discountRows[] = [
                    'order_id' => $order->id,
                    'order_item_id' => $createdOrderItem->id,
                    'discount_type' => 'product_discount',
                    'discount_code' => null,
                    'discount_name' => 'Product Discount',
                    'discount_rate' => null,
                    'discount_amount' => round((float) $itemSnapshot['product_discount_amount'] * (int) $createdOrderItem->quantity, 4),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (($itemSnapshot['bulk_discount_amount'] ?? 0) > 0) {
                $discountRows[] = [
                    'order_id' => $order->id,
                    'order_item_id' => $createdOrderItem->id,
                    'discount_type' => 'bulk',
                    'discount_code' => null,
                    'discount_name' => 'Bulk Price Benefit',
                    'discount_rate' => null,
                    'discount_amount' => round((float) $itemSnapshot['bulk_discount_amount'] * (int) $createdOrderItem->quantity, 4),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (($itemSnapshot['coupon_discount_amount'] ?? 0) > 0) {
                $discountRows[] = [
                    'order_id' => $order->id,
                    'order_item_id' => $createdOrderItem->id,
                    'discount_type' => 'coupon',
                    'discount_code' => $itemSnapshot['applied_coupon_code'] ?? null,
                    'discount_name' => 'Coupon Discount',
                    'discount_rate' => null,
                    'discount_amount' => round((float) $itemSnapshot['coupon_discount_amount'] * (int) $createdOrderItem->quantity, 4),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Step 3: insert all discount lines together.
        if ($discountRows !== []) {
            DB::table('order_discount_lines')->insert($discountRows);
        }
    }

    // This stores the selected shipping and billing addresses for a submitted order.
    protected function storeCheckoutOrderAddresses(Order $order, User $user, array $validatedCheckout): void
    {
        // Step 1: resolve the checkout address that the customer selected or entered.
        $checkoutAddressData = $this->resolveCheckoutAddressData($user, $validatedCheckout);

        // Step 2: create the shipping address row.
        $shippingAddressPayload = $this->buildOrderAddressPayload('shipping', $checkoutAddressData, $user, $validatedCheckout);
        $order->addresses()->create($shippingAddressPayload);

        // Step 3: create the billing address row.
        $billingAddressPayload = $this->buildOrderAddressPayload('billing', $checkoutAddressData, $user, $validatedCheckout);
        $order->addresses()->create($billingAddressPayload);
    }

    // This resolves the final checkout address from either a saved address or a new address form.
    protected function resolveCheckoutAddressData(User $user, array $validatedCheckout): array
    {
        // Step 1: decide whether checkout should use an existing or new address.
        $selectedAddressSource = (string) ($validatedCheckout['selected_address_source'] ?? 'existing');
        $checkoutAddress = null;
        $contactPhone = $user->phone;
        $addressLabel = null;

        // Step 2: load the selected saved address when the customer picked one.
        if ($selectedAddressSource === 'existing') {
            $checkoutAddress = UserAddress::query()
                ->where('user_id', $user->id)
                ->whereKey((int) ($validatedCheckout['selected_user_address_id'] ?? 0))
                ->first();

            if (! $checkoutAddress) {
                throw ValidationException::withMessages([
                    'selected_user_address_id' => 'Please select one saved address for checkout.',
                ]);
            }

            $addressLabel = $checkoutAddress->line2;
        }

        // Step 3: save and use the new checkout address when needed.
        if ($selectedAddressSource === 'new') {
            $checkoutAddress = $this->saveNewCheckoutAddressForUser($user, $validatedCheckout);
            $contactPhone = $validatedCheckout['new_address_phone'] ?? $user->phone;
            $addressLabel = $validatedCheckout['new_address_label'] ?? null;
        }

        // Step 4: return the final address details used for the order.
        return [
            'user_address' => $checkoutAddress,
            'address_line1' => $checkoutAddress->line1,
            'address_line2' => $checkoutAddress->line2,
            'city' => $checkoutAddress->city,
            'state' => $checkoutAddress->state,
            'postal_code' => $checkoutAddress->postal_code,
            'country' => $checkoutAddress->country ?: 'India',
            'contact_phone' => $contactPhone,
            'address_label' => $addressLabel,
        ];
    }

    // This saves a new checkout address into the user address book before it is used on the order.
    protected function saveNewCheckoutAddressForUser(User $user, array $validatedCheckout): UserAddress
    {
        // Step 1: check whether the user already has saved addresses.
        $userHasSavedAddresses = $user->addresses()->exists();

        // Step 2: save the new address in the user address book.
        return $user->addresses()->create([
            'line1' => trim((string) ($validatedCheckout['new_address_line1'] ?? '')),
            'line2' => filled($validatedCheckout['new_address_label'] ?? null) ? trim((string) $validatedCheckout['new_address_label']) : null,
            'city' => trim((string) ($validatedCheckout['new_address_city'] ?? '')),
            'state' => trim((string) ($validatedCheckout['new_address_state'] ?? '')),
            'postal_code' => trim((string) ($validatedCheckout['new_address_postal_code'] ?? '')),
            'country' => filled($validatedCheckout['new_address_country'] ?? null) ? trim((string) $validatedCheckout['new_address_country']) : 'India',
            'is_default_shipping' => ! $userHasSavedAddresses,
            'is_default_billing' => ! $userHasSavedAddresses,
        ]);
    }

    // This builds one order address payload from the chosen checkout address.
    protected function buildOrderAddressPayload(string $addressType, array $checkoutAddressData, User $user, array $validatedCheckout): array
    {
        // Step 1: resolve the company name for B2B billing details.
        $companyName = null;

        if (filled($validatedCheckout['registered_business_name'] ?? null)) {
            $companyName = trim((string) $validatedCheckout['registered_business_name']);
        } else {
            $companyName = $user->company?->legal_name ?: $user->company?->name;
        }

        // Step 2: keep the GST value only on billing flows for B2B users.
        $gstin = null;

        if ($addressType === 'billing' && $user->isB2b()) {
            if (filled($validatedCheckout['gstin'] ?? null)) {
                $gstin = trim((string) $validatedCheckout['gstin']);
            } else {
                $gstin = $user->company?->gst_number;
            }
        }

        // Step 3: return one order address payload for the given address type.
        return [
            'address_type' => $addressType,
            'contact_name' => $user->name,
            'company_name' => $user->isB2b() ? $companyName : null,
            'email' => $user->email,
            'phone' => filled($checkoutAddressData['contact_phone'] ?? null) ? trim((string) $checkoutAddressData['contact_phone']) : $user->phone,
            'gstin' => $gstin,
            'line1' => trim((string) ($checkoutAddressData['address_line1'] ?? '')),
            'line2' => filled($checkoutAddressData['address_line2'] ?? null) ? trim((string) $checkoutAddressData['address_line2']) : null,
            'landmark' => filled($checkoutAddressData['address_label'] ?? null) ? trim((string) $checkoutAddressData['address_label']) : null,
            'city' => trim((string) ($checkoutAddressData['city'] ?? '')),
            'state' => trim((string) ($checkoutAddressData['state'] ?? '')),
            'postal_code' => trim((string) ($checkoutAddressData['postal_code'] ?? '')),
            'country_code' => $this->normalizeCountryCode($checkoutAddressData['country'] ?? 'India'),
        ];
    }

    // This converts the entered country into the two-character code expected by order addresses.
    protected function normalizeCountryCode(?string $country): string
    {
        $normalizedCountry = strtoupper(trim((string) $country));

        return match ($normalizedCountry) {
            '', 'INDIA', 'IN' => 'IN',
            default => substr($normalizedCountry, 0, 2),
        };
    }

    // This sends the order-submitted customer email without interrupting a successful checkout.
    protected function sendOrderSubmittedEmail(User $user, Order $order): void
    {
        try {
            // Step 1: skip the send cleanly when the account does not have an email address yet.
            if (! filled($user->email)) {
                Log::warning('Order submitted email skipped because the user email is empty.', [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                ]);

                return;
            }

            // Step 2: send the confirmation email using the shared notification service.
            $this->emailNotificationService->sendOrderSubmittedConfirmation($user, $order);
        } catch (Throwable $exception) {
            Log::error('Order submitted email could not be delivered after checkout.', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
