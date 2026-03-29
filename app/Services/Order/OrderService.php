<?php

namespace App\Services\Order;

use App\Models\Authorization\User;
use App\Models\Authorization\UserAddress;
use App\Models\Order\Order;
use App\Models\Product\ProductVariant;
use App\Services\Authorization\DataVisibilityService;
use App\Services\Coupon\CouponService;
use App\Services\Notification\EmailNotificationService;
use App\Services\Pricing\PriceService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderService
{
    public function __construct(
        protected DataVisibilityService $dataVisibilityService,
        protected PriceService $priceService,
        protected CouponService $couponService,
        protected EmailNotificationService $emailNotificationService,
    ) {
    }

    // This prepares the customer profile orders page using the existing UI data shape.
    public function customerOrdersPageData(User $user): array
    {
        try {
            // Step 1: start the signed-in user's profile order query.
            $orderQuery = Order::query();
            $orderQuery->with([
                'shippingAddress',
                'billingAddress',
                'items',
                'items.product.primaryImage',
            ]);
            $orderQuery->where('placed_by_user_id', $user->id);
            $orderQuery->orderByDesc('created_at');
            $orderQuery->orderByDesc('id');

            // Step 2: load the final orders with the related data used by the original UI.
            $savedOrders = $orderQuery->get();

            // Step 3: prepare the final preview card data one order at a time.
            $orders = [];
            $fallbackImages = [
                asset('upload/categories/image1.jpg'),
                asset('upload/categories/image2.jpg'),
                asset('upload/categories/image5.jpg'),
            ];

            foreach ($savedOrders as $orderIndex => $savedOrder) {
                // Step 4: prepare the order item rows for the modal and card.
                $preparedItems = [];
                $firstPreparedItem = null;
                $orderCurrency = $savedOrder->currency ?: 'INR';

                foreach ($savedOrder->items as $itemIndex => $savedItem) {
                    $productImagePath = $savedItem->product?->primaryImage?->file_path;
                    $imageUrl = $productImagePath ? asset($productImagePath) : $fallbackImages[$itemIndex % count($fallbackImages)];
                    $itemBackground = $itemIndex % 2 === 0 ? 'bg-primary-50' : 'bg-slate-50';
                    $itemSubtitle = $savedItem->variant_name ?: 'Order item';
                    $preparedItem = [
                        'name' => $savedItem->product_name,
                        'subtitle' => $itemSubtitle,
                        'sku' => $savedItem->sku ?: 'N/A',
                        'qty' => (int) $savedItem->quantity,
                        'price' => $orderCurrency.' '.number_format((float) $savedItem->unit_price, 2),
                        'total' => $orderCurrency.' '.number_format((float) $savedItem->total_amount, 2),
                        'image' => $imageUrl,
                        'background' => $itemBackground,
                    ];

                    $preparedItems[] = $preparedItem;

                    if (! $firstPreparedItem) {
                        $firstPreparedItem = $preparedItem;
                    }
                }

                // Step 5: prepare the address lines shown in the original modal.
                $displayAddress = $savedOrder->shippingAddress ?: $savedOrder->billingAddress;
                $addressLines = [];

                if ($displayAddress) {
                    if ($displayAddress->company_name) {
                        $addressLines[] = $displayAddress->company_name;
                    }

                    if ($displayAddress->contact_name) {
                        $addressLines[] = $displayAddress->contact_name;
                    }

                    $addressLines[] = $displayAddress->line1;

                    if ($displayAddress->line2) {
                        $addressLines[] = $displayAddress->line2;
                    }

                    $cityLine = trim($displayAddress->city.', '.$displayAddress->state.' '.$displayAddress->postal_code);
                    $addressLines[] = $cityLine;
                }

                if ($addressLines === []) {
                    $addressLines[] = 'Address details not available';
                }

                // Step 6: map the backend status into the existing UI color system.
                $statusMeta = [
                    'label' => ucfirst((string) $savedOrder->status),
                    'key' => 'processing',
                ];

                if ($savedOrder->status === 'submitted') {
                    $statusMeta['key'] = 'shipped';
                }

                if ($savedOrder->status === 'cancelled') {
                    $statusMeta['key'] = 'archived';
                }

                // Step 7: prepare the main product title shown on the card.
                $productTitle = 'Order #'.$savedOrder->id;

                if ($firstPreparedItem) {
                    $productTitle = $firstPreparedItem['name'];

                    if (count($preparedItems) > 1) {
                        $productTitle = $firstPreparedItem['name'].' + '.(count($preparedItems) - 1).' more items';
                    }
                }

                // Step 8: prepare the summary note while keeping the old UI text slot filled.
                $summaryNote = trim((string) ($savedOrder->notes ?? ''));

                if ($summaryNote === '') {
                    $itemCount = count($preparedItems);
                    $summaryNote = $itemCount === 1 ? '1 item in this order' : $itemCount.' items in this order';
                }

                // Step 9: prepare the main order image and image background.
                $cardImage = $firstPreparedItem['image'] ?? $fallbackImages[$orderIndex % count($fallbackImages)];
                $cardBackground = $firstPreparedItem ? $firstPreparedItem['background'] : 'bg-slate-50';

                // Step 10: add the final order row in the exact structure used by the original blade.
                $orders[] = [
                    'id' => 'ORD-'.str_pad((string) $savedOrder->id, 6, '0', STR_PAD_LEFT),
                    'reference' => '#ORD-'.str_pad((string) $savedOrder->id, 6, '0', STR_PAD_LEFT),
                    'order_id' => $savedOrder->id,
                    'reorder_url' => route('orders.reorder', $savedOrder->id),
                    'status' => $statusMeta['label'],
                    'status_key' => $statusMeta['key'],
                    'product' => $productTitle,
                    'date' => optional($savedOrder->submitted_at ?: $savedOrder->created_at)->format('M d, Y') ?: 'N/A',
                    'total' => $orderCurrency.' '.number_format((float) $savedOrder->total_amount, 2),
                    'image' => $cardImage,
                    'image_background' => $cardBackground,
                    'summary_note' => $summaryNote,
                    'tracking_id' => 'Order #'.$savedOrder->id,
                    'carrier' => 'Live shipment tracking is not available yet.',
                    'address_lines' => $addressLines,
                    'subtotal' => $orderCurrency.' '.number_format((float) $savedOrder->subtotal_amount, 2),
                    'tax' => $orderCurrency.' '.number_format((float) $savedOrder->tax_amount, 2),
                    'shipping' => $orderCurrency.' '.number_format((float) $savedOrder->shipping_amount, 2),
                    'grand_total' => $orderCurrency.' '.number_format((float) $savedOrder->total_amount, 2),
                    'invoice_note' => null,
                    'items' => $preparedItems,
                ];
            }

            // Step 11: return the final page data for the original profile orders blade.
            return [
                'orders' => $orders,
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build customer profile orders page data.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This prepares a reorder-only checkout seed without changing the saved cart.
    public function ReOrder(int $orderId, User $user, Request $request): void
    {
        try {
            // Step 1: load the selected order that belongs to the current user.
            $order = $this->getOrderById($orderId, $user);
            $fallbackImage = asset('upload/categories/image1.jpg');

            // Step 2: stop the flow when the selected order has no items left to reorder.
            if ($order->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'order' => 'This order does not have any items to reorder.',
                ]);
            }

            // Step 3: prepare fresh checkout items using the latest current pricing.
            $checkoutItems = [];
            $subtotalAmount = 0;
            $taxAmount = 0;
            $totalAmount = 0;
            $currency = $order->currency ?: 'INR';

            foreach ($order->items as $orderItem) {
                $productId = (int) $orderItem->product_id;
                $productVariantId = $orderItem->product_variant_id ? (int) $orderItem->product_variant_id : null;
                $quantity = (int) $orderItem->quantity;
                $resolvedPrice = null;

                if ($productId === 0) {
                    $productId = (int) ($orderItem->product?->id ?? 0);
                }

                // Step 4: resolve the latest live price for the saved product or variant.
                if ($productVariantId) {
                    $resolvedPrice = $this->priceService->resolveVariantPrice($productVariantId, $user, $quantity);
                }

                if (! $productVariantId) {
                    $resolvedPrice = $this->priceService->resolveProductPrice($productId, $user, $quantity);
                }

                if (! $resolvedPrice) {
                    throw ValidationException::withMessages([
                        'order' => 'One order item is no longer available for reorder.',
                    ]);
                }

                // Step 5: calculate the current line values that checkout should show.
                $unitPrice = round((float) ($resolvedPrice['amount'] ?? 0), 4);
                $unitTaxAmount = round((float) ($resolvedPrice['tax_amount'] ?? 0), 4);
                $unitPriceAfterGst = round((float) ($resolvedPrice['price_after_gst'] ?? ($unitPrice + $unitTaxAmount)), 4);
                $lineSubtotal = round($unitPrice * $quantity, 4);
                $lineTaxAmount = round($unitTaxAmount * $quantity, 4);
                $lineTotal = round($unitPriceAfterGst * $quantity, 4);
                $imagePath = $orderItem->product?->primaryImage?->file_path;
                $imageUrl = $fallbackImage;
                $currency = (string) ($resolvedPrice['currency'] ?? $currency);

                if (filled($imagePath)) {
                    $imageUrl = asset($imagePath);
                }

                // Step 6: add the prepared item in the same simple shape used by checkout JS.
                $checkoutItems[] = [
                    'productId' => $productId,
                    'variantId' => $productVariantId,
                    'quantity' => $quantity,
                    'unitPrice' => $unitPrice,
                    'unitTaxAmount' => $unitTaxAmount,
                    'unitPriceAfterGst' => $unitPriceAfterGst,
                    'taxAmount' => $lineTaxAmount,
                    'lineSubtotal' => $lineSubtotal,
                    'lineTotal' => $lineTotal,
                    'discountAmount' => round((float) ($resolvedPrice['discount_amount'] ?? 0) * $quantity, 4),
                    'currency' => $currency,
                    'priceType' => $resolvedPrice['price_type'] ?? null,
                    'name' => (string) ($orderItem->product_name ?: 'Product'),
                    'model' => (string) ($orderItem->sku ?: 'N/A'),
                    'image' => $imageUrl,
                    'minOrderQuantity' => (int) ($resolvedPrice['min_order_quantity'] ?? 1),
                    'maxOrderQuantity' => $resolvedPrice['max_order_quantity'] === null ? null : (int) $resolvedPrice['max_order_quantity'],
                    'lotSize' => (int) ($resolvedPrice['lot_size'] ?? 1),
                ];

                $subtotalAmount += $lineSubtotal;
                $taxAmount += $lineTaxAmount;
                $totalAmount += $lineTotal;
            }

            // Step 7: store the reorder checkout seed in session for the checkout page.
            $request->session()->put('reorder_checkout', [
                'source' => 'reorder',
                'orderId' => $order->id,
                'currency' => $currency,
                'items_count' => count($checkoutItems),
                'subtotal_amount' => round($subtotalAmount, 4),
                'tax_amount' => round($taxAmount, 4),
                'total_amount' => round($totalAmount, 4),
                'items' => $checkoutItems,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to prepare reorder checkout seed.', [
                'order_id' => $orderId,
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This submits the separate reorder checkout using the latest live pricing.
    public function submitReOrderCheckout(array $validatedCheckout, User $user): array
    {
        try {
            // Step 1: validate the coupon code before preparing the final order items.
            $couponCode = $this->couponService->readValidatedCouponCode($validatedCheckout['coupon_code'] ?? null);

            // Step 2: prepare the final order items from the posted reorder payload.
            $preparedOrderItems = $this->prepareReOrderCheckoutItems($validatedCheckout, $user, $couponCode);

            // Step 3: stop checkout when the coupon is valid but did not apply to any reorder item.
            $this->couponService->ensureCouponAppliesToPreparedItems(
                $couponCode,
                $preparedOrderItems,
                'The selected coupon does not apply to the current reorder items.',
            );

            // Step 4: calculate the final order totals from the prepared items.
            $orderTotals = $this->calculateReOrderCheckoutTotals($validatedCheckout, $preparedOrderItems);

            // Step 5: resolve the selected checkout address before saving the order.
            $checkoutAddressData = $this->resolveReOrderCheckoutAddress($user, $validatedCheckout);

            // Step 6: create the order, items, and addresses inside one transaction.
            $order = DB::transaction(function () use ($user, $validatedCheckout, $preparedOrderItems, $orderTotals, $checkoutAddressData): Order {
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

                foreach ($preparedOrderItems as $preparedOrderItem) {
                    $order->items()->create($preparedOrderItem);
                }

                // Step 7: save the shipping and billing addresses on the order.
                $shippingAddressPayload = $this->buildReOrderAddressPayload($order, 'shipping', $checkoutAddressData, $user, $validatedCheckout);
                $billingAddressPayload = $this->buildReOrderAddressPayload($order, 'billing', $checkoutAddressData, $user, $validatedCheckout);
                $order->addresses()->create($shippingAddressPayload);
                $order->addresses()->create($billingAddressPayload);

                return Order::query()
                    ->with([
                        'items' => fn ($builder) => $builder->orderBy('sort_order')->orderBy('id'),
                    ])
                    ->findOrFail($order->id);
            });

            // Step 8: send the customer confirmation email after the order is safely created.
            $this->sendReOrderSubmittedEmail($user, $order);

            // Step 9: build the simple order summary used by confirmation page.
            $orderItems = [];

            foreach ($order->items as $orderItem) {
                $orderItems[] = [
                    'id' => $orderItem->id,
                    'product_name' => $orderItem->product_name,
                    'variant_name' => $orderItem->variant_name,
                    'sku' => $orderItem->sku,
                    'quantity' => (int) $orderItem->quantity,
                    'unit_price' => (float) $orderItem->unit_price,
                    'tax_amount' => (float) $orderItem->tax_amount,
                    'total_amount' => (float) $orderItem->total_amount,
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
            Log::error('Failed to submit reorder checkout.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This prepares the live coupon preview used by reorder checkout AJAX.
    public function previewReOrderCoupon(array $validatedCouponInput, User $user): array
    {
        $couponPreview = [];

        try {
            // Step 1: validate the entered coupon code.
            $couponCode = $this->couponService->readValidatedCouponCode($validatedCouponInput['coupon_code'] ?? null);

            // Step 2: prepare the reorder items using live coupon-aware pricing.
            $preparedOrderItems = $this->prepareReOrderCheckoutItems($validatedCouponInput, $user, $couponCode);

            // Step 3: stop the flow when the coupon does not affect any reorder item.
            $this->couponService->ensureCouponAppliesToPreparedItems(
                $couponCode,
                $preparedOrderItems,
                'The selected coupon does not apply to the current reorder items.',
            );

            // Step 4: prepare the final preview summary for the checkout UI.
            $couponPreview = $this->couponService->buildCouponPreview(
                $couponCode,
                $preparedOrderItems,
                'Coupon applied successfully.',
                'The selected coupon does not apply to the current reorder items.',
            );
        } catch (Throwable $exception) {
            Log::error('Failed to preview reorder coupon.', [
                'user_id' => $user->id,
                'coupon_code' => $validatedCouponInput['coupon_code'] ?? null,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $couponPreview;
    }

    // This prepares live reorder item pricing used when quantity changes on checkout.
    public function previewReOrderPricing(array $validatedPreviewInput, User $user): array
    {
        $reOrderPreview = [];

        try {
            // Step 1: read the current applied coupon only when one code was already applied.
            $couponCode = null;
            $enteredCouponCode = trim((string) ($validatedPreviewInput['coupon_code'] ?? ''));

            if ($enteredCouponCode !== '') {
                $couponCode = $this->couponService->readValidatedCouponCode($enteredCouponCode);
            }

            // Step 2: prepare the reorder items again using the latest live pricing.
            $preparedOrderItems = $this->prepareReOrderCheckoutItems($validatedPreviewInput, $user, $couponCode);

            // Step 3: decode the posted reorder items so image and display labels stay unchanged.
            $decodedReOrderItems = json_decode((string) ($validatedPreviewInput['reorder_items'] ?? ''), true);

            if (! is_array($decodedReOrderItems)) {
                throw ValidationException::withMessages([
                    'reorder_items' => 'Reorder items are not available for checkout.',
                ]);
            }

            // Step 4: build the refreshed checkout items in the same shape already used by the UI.
            $previewItems = [];

            foreach ($preparedOrderItems as $index => $preparedOrderItem) {
                $sourceItem = is_array($decodedReOrderItems[$index] ?? null) ? $decodedReOrderItems[$index] : [];
                $itemSnapshot = is_array($preparedOrderItem['item_snapshot'] ?? null) ? $preparedOrderItem['item_snapshot'] : [];
                $quantity = (int) ($preparedOrderItem['quantity'] ?? 0);
                $unitPrice = round((float) ($preparedOrderItem['unit_price'] ?? 0), 4);
                $unitTaxAmount = round((float) ($itemSnapshot['unit_tax_amount'] ?? 0), 4);
                $unitPriceAfterGst = round((float) ($itemSnapshot['unit_price_after_gst'] ?? ($unitPrice + $unitTaxAmount)), 4);
                $lineSubtotal = round((float) ($preparedOrderItem['subtotal_amount'] ?? 0), 4);
                $lineTaxAmount = round((float) ($preparedOrderItem['tax_amount'] ?? 0), 4);
                $lineTotal = round((float) ($preparedOrderItem['total_amount'] ?? 0), 4);
                $lineDiscountAmount = round((float) ($preparedOrderItem['discount_amount'] ?? 0), 4);
                $productId = (int) ($preparedOrderItem['product_id'] ?? ($sourceItem['productId'] ?? 0));
                $productVariantId = $preparedOrderItem['product_variant_id'] ?? ($sourceItem['variantId'] ?? null);
                $imageUrl = (string) ($sourceItem['image'] ?? $sourceItem['image_url'] ?? 'https://via.placeholder.com/96x96?text=Bio');
                $productName = (string) ($preparedOrderItem['product_name'] ?? ($sourceItem['name'] ?? 'Product'));
                $productModel = (string) ($preparedOrderItem['sku'] ?? ($sourceItem['model'] ?? 'N/A'));

                if ($productVariantId !== null && $productVariantId !== '') {
                    $productVariantId = (int) $productVariantId;
                } else {
                    $productVariantId = null;
                }

                $previewItems[] = [
                    'productId' => $productId,
                    'variantId' => $productVariantId,
                    'quantity' => $quantity,
                    'unitPrice' => $unitPrice,
                    'unitTaxAmount' => $unitTaxAmount,
                    'unitPriceAfterGst' => $unitPriceAfterGst,
                    'taxAmount' => $lineTaxAmount,
                    'lineSubtotal' => $lineSubtotal,
                    'lineTotal' => $lineTotal,
                    'discountAmount' => $lineDiscountAmount,
                    'currency' => (string) ($itemSnapshot['currency'] ?? 'INR'),
                    'priceType' => $itemSnapshot['price_type'] ?? null,
                    'name' => $productName,
                    'model' => $productModel,
                    'image' => $imageUrl,
                    'minOrderQuantity' => (int) ($itemSnapshot['min_order_quantity'] ?? 1),
                    'maxOrderQuantity' => $itemSnapshot['max_order_quantity'] === null ? null : (int) $itemSnapshot['max_order_quantity'],
                    'lotSize' => (int) ($itemSnapshot['lot_size'] ?? 1),
                ];
            }

            // Step 5: prepare the refreshed coupon preview when one coupon is active.
            $couponPreview = null;

            if ($couponCode !== null) {
                $couponPreview = $this->couponService->buildCouponPreview(
                    $couponCode,
                    $preparedOrderItems,
                    'Coupon applied successfully.',
                    'The selected coupon does not apply to the current reorder items.',
                );
            }

            // Step 6: return the refreshed items and coupon details for the checkout UI.
            $reOrderPreview = [
                'items' => $previewItems,
                'coupon_preview' => $couponPreview,
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to preview reorder pricing.', [
                'user_id' => $user->id,
                'coupon_code' => $validatedPreviewInput['coupon_code'] ?? null,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $reOrderPreview;
    }

    // This prepares the final reorder checkout items from the posted checkout payload.
    protected function prepareReOrderCheckoutItems(array $validatedCheckout, User $user, ?string $couponCode = null): array
    {
        try {
            // Step 1: decode the posted reorder item JSON.
            $encodedReOrderItems = (string) ($validatedCheckout['reorder_items'] ?? '');
            $decodedReOrderItems = json_decode($encodedReOrderItems, true);

            if (! is_array($decodedReOrderItems)) {
                throw ValidationException::withMessages([
                    'reorder_items' => 'Reorder items are not available for checkout.',
                ]);
            }

            // Step 2: prepare one order item at a time using the latest live price.
            $preparedOrderItems = [];

            foreach ($decodedReOrderItems as $index => $decodedReOrderItem) {
                $productId = (int) ($decodedReOrderItem['productId'] ?? 0);
                $productVariantId = $decodedReOrderItem['variantId'] ?? null;
                $quantity = (int) ($decodedReOrderItem['quantity'] ?? 0);

                if ($productVariantId !== null && $productVariantId !== '') {
                    $productVariantId = (int) $productVariantId;
                }

                if ($productVariantId === '') {
                    $productVariantId = null;
                }

                if ($quantity <= 0) {
                    continue;
                }

                if ($productId === 0 && ! $productVariantId) {
                    continue;
                }

                $resolvedPrice = null;
                $productVariant = null;
                $visibleProduct = null;

                // Step 3: resolve the live price from variant when variant id is available.
                if ($productVariantId) {
                    $resolvedPrice = $this->priceService->resolveVariantPrice($productVariantId, $user, $quantity, $couponCode);
                    $productVariant = ProductVariant::query()
                        ->with('product:id,name')
                        ->find($productVariantId);
                    $visibleProduct = $productVariant?->product;
                }

                // Step 4: resolve the live price from product when variant id is not available.
                if (! $productVariantId) {
                    $resolvedPrice = $this->priceService->resolveProductPrice($productId, $user, $quantity, $couponCode);

                    if (($resolvedPrice['product_variant_id'] ?? null) !== null) {
                        $productVariantId = (int) $resolvedPrice['product_variant_id'];
                        $productVariant = ProductVariant::query()
                            ->with('product:id,name')
                            ->find($productVariantId);
                        $visibleProduct = $productVariant?->product;
                    }

                    if (! $visibleProduct) {
                        $visibleProduct = $this->dataVisibilityService->visibleProductQuery($user)
                            ->where('products.id', $productId)
                            ->first();
                    }
                }

                if (! $resolvedPrice) {
                    throw ValidationException::withMessages([
                        'reorder_items' => 'One reorder item is no longer available for checkout.',
                    ]);
                }

                // Step 5: validate the latest min, max, and lot-size rules.
                $this->validateOrderQuantity($quantity, $resolvedPrice, $index);

                // Step 6: calculate the final amounts for this order item.
                $unitPrice = round((float) ($resolvedPrice['amount'] ?? 0), 4);
                $unitBasePrice = round((float) ($resolvedPrice['base_amount'] ?? $unitPrice), 4);
                $unitTaxAmount = round((float) ($resolvedPrice['tax_amount'] ?? 0), 4);
                $unitPriceAfterGst = round((float) ($resolvedPrice['price_after_gst'] ?? ($unitPrice + $unitTaxAmount)), 4);
                $subtotalAmount = round($unitPrice * $quantity, 4);
                $taxAmount = round($unitTaxAmount * $quantity, 4);
                $discountAmount = round((float) ($resolvedPrice['discount_amount'] ?? 0) * $quantity, 4);
                $totalAmount = round($unitPriceAfterGst * $quantity, 4);
                $productName = (string) ($visibleProduct?->name ?: ($decodedReOrderItem['name'] ?? 'Product'));
                $variantName = $productVariant?->variant_name ?: ($resolvedPrice['variant_name'] ?? null);
                $sku = $productVariant?->sku ?: ($resolvedPrice['variant_sku'] ?? ($decodedReOrderItem['model'] ?? 'N/A'));

                // Step 7: add the final prepared order item.
                $preparedOrderItems[] = [
                    'product_id' => $visibleProduct?->id ?: $productId,
                    'product_variant_id' => $productVariantId,
                    'sku' => (string) $sku,
                    'product_name' => $productName,
                    'variant_name' => $variantName,
                    'description' => 'Created from reorder checkout.',
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal_amount' => $subtotalAmount,
                    'discount_amount' => $discountAmount,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'sort_order' => $index,
                    'item_snapshot' => [
                        'source' => 'reorder_checkout',
                        'currency' => $resolvedPrice['currency'] ?? 'INR',
                        'price_type' => $resolvedPrice['price_type'] ?? null,
                        'base_unit_price' => $unitBasePrice,
                        'pricing_stage' => $resolvedPrice['pricing_stage'] ?? 'base_price',
                        'gst_rate' => round((float) ($resolvedPrice['gst_rate'] ?? 0), 4),
                        'unit_tax_amount' => $unitTaxAmount,
                        'unit_price_after_gst' => $unitPriceAfterGst,
                        'unit_discount_amount' => round((float) ($resolvedPrice['discount_amount'] ?? 0), 4),
                        'product_discount_amount' => round((float) ($resolvedPrice['product_discount_amount'] ?? 0), 4),
                        'bulk_discount_amount' => round((float) ($resolvedPrice['bulk_discount_amount'] ?? 0), 4),
                        'coupon_discount_amount' => round((float) ($resolvedPrice['coupon_discount_amount'] ?? 0), 4),
                        'applied_coupon_code' => $resolvedPrice['applied_coupon_code'] ?? null,
                        'coupon_status' => $resolvedPrice['coupon_status'] ?? null,
                        'coupon_message' => $resolvedPrice['coupon_message'] ?? null,
                        'min_order_quantity' => (int) ($resolvedPrice['min_order_quantity'] ?? 1),
                        'max_order_quantity' => $resolvedPrice['max_order_quantity'] === null ? null : (int) $resolvedPrice['max_order_quantity'],
                        'lot_size' => (int) ($resolvedPrice['lot_size'] ?? 1),
                        'variant_sku' => (string) $sku,
                        'variant_name' => $variantName,
                    ],
                ];
            }

            // Step 8: stop checkout when no valid reorder items remain.
            if ($preparedOrderItems === []) {
                throw ValidationException::withMessages([
                    'reorder_items' => 'No reorder items are available for checkout.',
                ]);
            }

            return $preparedOrderItems;
        } catch (Throwable $exception) {
            Log::error('Failed to prepare reorder checkout items.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This calculates the final reorder checkout totals from the prepared items.
    protected function calculateReOrderCheckoutTotals(array $validatedCheckout, array $preparedOrderItems): array
    {
        try {
            // Step 1: read the optional header amounts.
            $shippingAmount = round((float) ($validatedCheckout['shipping_amount'] ?? 0), 4);
            $adjustmentAmount = round((float) ($validatedCheckout['adjustment_amount'] ?? 0), 4);
            $roundingAmount = round((float) ($validatedCheckout['rounding_amount'] ?? 0), 4);
            $currency = (string) ($preparedOrderItems[0]['item_snapshot']['currency'] ?? 'INR');

            // Step 2: sum all order item amounts.
            $subtotalAmount = 0;
            $taxAmount = 0;
            $discountAmount = 0;
            $itemsTotal = 0;

            foreach ($preparedOrderItems as $preparedOrderItem) {
                $subtotalAmount += (float) ($preparedOrderItem['subtotal_amount'] ?? 0);
                $taxAmount += (float) ($preparedOrderItem['tax_amount'] ?? 0);
                $discountAmount += (float) ($preparedOrderItem['discount_amount'] ?? 0);
                $itemsTotal += (float) ($preparedOrderItem['total_amount'] ?? 0);
            }

            $subtotalAmount = round($subtotalAmount, 4);
            $taxAmount = round($taxAmount, 4);
            $discountAmount = round($discountAmount, 4);
            $itemsTotal = round($itemsTotal, 4);
            $totalAmount = round($itemsTotal + $shippingAmount + $adjustmentAmount + $roundingAmount, 4);

            // Step 3: build the pricing snapshot stored on the order header.
            $pricingSnapshot = [
                'source' => 'reorder_checkout',
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
            ];

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
        } catch (Throwable $exception) {
            Log::error('Failed to calculate reorder checkout totals.', [
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This resolves the final checkout address from either saved address or new address form.
    protected function resolveReOrderCheckoutAddress(User $user, array $validatedCheckout): array
    {
        try {
            $selectedAddressSource = (string) ($validatedCheckout['selected_address_source'] ?? 'existing');

            // Step 1: reuse one saved address when the customer selected an existing address.
            if ($selectedAddressSource === 'existing') {
                $selectedUserAddressId = (int) ($validatedCheckout['selected_user_address_id'] ?? 0);

                if ($selectedUserAddressId === 0) {
                    throw ValidationException::withMessages([
                        'selected_user_address_id' => 'Please select one saved address for checkout.',
                    ]);
                }

                $selectedUserAddress = UserAddress::query()
                    ->where('user_id', $user->id)
                    ->whereKey($selectedUserAddressId)
                    ->first();

                if (! $selectedUserAddress) {
                    throw ValidationException::withMessages([
                        'selected_user_address_id' => 'Please select one saved address for checkout.',
                    ]);
                }

                return [
                    'address_line1' => $selectedUserAddress->line1,
                    'address_line2' => $selectedUserAddress->line2,
                    'city' => $selectedUserAddress->city,
                    'state' => $selectedUserAddress->state,
                    'postal_code' => $selectedUserAddress->postal_code,
                    'country' => $selectedUserAddress->country ?: 'India',
                    'contact_phone' => $user->phone,
                    'address_label' => $selectedUserAddress->line2,
                ];
            }

            // Step 2: validate the new address fields before saving them.
            if (! filled($validatedCheckout['new_address_label'] ?? null)) {
                throw ValidationException::withMessages([
                    'new_address_label' => 'Please enter an address label.',
                ]);
            }

            if (! filled($validatedCheckout['new_address_line1'] ?? null)) {
                throw ValidationException::withMessages([
                    'new_address_line1' => 'Please enter a street address.',
                ]);
            }

            if (! filled($validatedCheckout['new_address_city'] ?? null)) {
                throw ValidationException::withMessages([
                    'new_address_city' => 'Please enter a city.',
                ]);
            }

            if (! filled($validatedCheckout['new_address_state'] ?? null)) {
                throw ValidationException::withMessages([
                    'new_address_state' => 'Please enter a state.',
                ]);
            }

            if (! filled($validatedCheckout['new_address_postal_code'] ?? null)) {
                throw ValidationException::withMessages([
                    'new_address_postal_code' => 'Please enter a postal code.',
                ]);
            }

            // Step 3: save the new address for reuse in future checkout flows.
            $createdUserAddress = $this->saveReOrderCheckoutAddress($user, $validatedCheckout);

            return [
                'address_line1' => $createdUserAddress->line1,
                'address_line2' => $createdUserAddress->line2,
                'city' => $createdUserAddress->city,
                'state' => $createdUserAddress->state,
                'postal_code' => $createdUserAddress->postal_code,
                'country' => $createdUserAddress->country ?: 'India',
                'contact_phone' => $validatedCheckout['new_address_phone'] ?? $user->phone,
                'address_label' => $validatedCheckout['new_address_label'] ?? null,
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to resolve reorder checkout address.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This saves a new checkout address for the reorder flow.
    protected function saveReOrderCheckoutAddress(User $user, array $validatedCheckout): UserAddress
    {
        try {
            $userHasSavedAddresses = $user->addresses()->exists();

            // Step 1: save the new address in the user address book.
            $createdUserAddress = $user->addresses()->create([
                'line1' => trim((string) ($validatedCheckout['new_address_line1'] ?? '')),
                'line2' => filled($validatedCheckout['new_address_label'] ?? null) ? trim((string) $validatedCheckout['new_address_label']) : null,
                'city' => trim((string) ($validatedCheckout['new_address_city'] ?? '')),
                'state' => trim((string) ($validatedCheckout['new_address_state'] ?? '')),
                'postal_code' => trim((string) ($validatedCheckout['new_address_postal_code'] ?? '')),
                'country' => filled($validatedCheckout['new_address_country'] ?? null) ? trim((string) $validatedCheckout['new_address_country']) : 'India',
                'is_default_shipping' => ! $userHasSavedAddresses,
                'is_default_billing' => ! $userHasSavedAddresses,
            ]);

            return $createdUserAddress;
        } catch (Throwable $exception) {
            Log::error('Failed to save reorder checkout address.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This builds one order address payload for shipping or billing.
    protected function buildReOrderAddressPayload(Order $order, string $addressType, array $checkoutAddressData, User $user, array $validatedCheckout): array
    {
        try {
            $companyName = null;

            if ($user->isB2b()) {
                $companyName = $validatedCheckout['registered_business_name'] ?? null;

                if (! filled($companyName)) {
                    $companyName = $user->company?->legal_name ?: $user->company?->name;
                }
            }

            $gstin = null;

            if ($addressType === 'billing' && $user->isB2b()) {
                $gstin = $validatedCheckout['gstin'] ?? null;

                if (! filled($gstin)) {
                    $gstin = $user->company?->gst_number;
                }
            }

            return [
                'order_id' => $order->id,
                'address_type' => $addressType,
                'contact_name' => $user->name,
                'company_name' => $companyName,
                'email' => $user->email,
                'phone' => filled($checkoutAddressData['contact_phone'] ?? null) ? trim((string) $checkoutAddressData['contact_phone']) : $user->phone,
                'gstin' => filled($gstin) ? trim((string) $gstin) : null,
                'line1' => trim((string) ($checkoutAddressData['address_line1'] ?? '')),
                'line2' => filled($checkoutAddressData['address_line2'] ?? null) ? trim((string) $checkoutAddressData['address_line2']) : null,
                'landmark' => filled($checkoutAddressData['address_label'] ?? null) ? trim((string) $checkoutAddressData['address_label']) : null,
                'city' => trim((string) ($checkoutAddressData['city'] ?? '')),
                'state' => trim((string) ($checkoutAddressData['state'] ?? '')),
                'postal_code' => trim((string) ($checkoutAddressData['postal_code'] ?? '')),
                'country_code' => $this->normalizeReOrderCountryCode($checkoutAddressData['country'] ?? 'India'),
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build reorder order address payload.', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'address_type' => $addressType,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This converts the entered country into the code stored on order addresses.
    protected function normalizeReOrderCountryCode(?string $country): string
    {
        $normalizedCountry = strtoupper(trim((string) $country));

        return match ($normalizedCountry) {
            '', 'INDIA', 'IN' => 'IN',
            default => substr($normalizedCountry, 0, 2),
        };
    }

    // This sends the reorder confirmation email after successful order placement.
    protected function sendReOrderSubmittedEmail(User $user, Order $order): void
    {
        try {
            if (! filled($user->email)) {
                return;
            }

            $this->emailNotificationService->sendOrderSubmittedConfirmation($user, $order);
        } catch (Throwable $exception) {
            Log::error('Failed to send reorder confirmation email.', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    // This prepares the order CRUD page with products, current orders, and optional edit data.
    public function orderCrudPageData(User $user, ?int $editingOrderId = null): array
    {
        try {
            // Step 1: load all visible products with their resolved order price details.
            $products = $this->loadVisibleProducts($user);

            // Step 2: load existing non-deleted orders for the current user.
            $orders = $this->listOrdersForUser($user);

            // Step 3: load one order for edit when an edit id is passed.
            $editingOrder = null;
            $editingItems = collect();

            if ($editingOrderId) {
                $editingOrder = $this->getOrderById($editingOrderId, $user);
                $editingItems = $editingOrder->items->sortBy('sort_order')->values();
            }

            // Step 4: return all page data to the order CRUD blade.
            return [
                'products' => $products,
                'orders' => $orders,
                'editingOrder' => $editingOrder,
                'editingItems' => $editingItems,
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build order CRUD page data.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This creates one order and all of its item rows for the current user.
    public function createOrder(array $validatedOrder, User $user): Order
    {
        try {
            // Step 1: prepare all item rows from the submitted products and quantities.
            $preparedOrderItems = $this->prepareOrderItems($validatedOrder, $user);

            // Step 2: calculate the final order totals from item rows and extra amounts.
            $orderTotals = $this->calculateOrderTotals($validatedOrder, $preparedOrderItems);

            // Step 3: save the order header and order items inside one transaction.
            $order = DB::transaction(function () use ($validatedOrder, $user, $preparedOrderItems, $orderTotals): Order {
                $order = Order::query()->create([
                    'placed_by_user_id' => $user->id,
                    'company_id' => $user->company_id,
                    'status' => $validatedOrder['status'],
                    'currency' => $orderTotals['currency'],
                    'subtotal_amount' => $orderTotals['subtotal_amount'],
                    'tax_amount' => $orderTotals['tax_amount'],
                    'discount_amount' => $orderTotals['discount_amount'],
                    'shipping_amount' => $orderTotals['shipping_amount'],
                    'adjustment_amount' => $orderTotals['adjustment_amount'],
                    'rounding_amount' => $orderTotals['rounding_amount'],
                    'total_amount' => $orderTotals['total_amount'],
                    'pricing_snapshot' => $orderTotals['pricing_snapshot'],
                    'notes' => $validatedOrder['notes'] ?: null,
                    'submitted_at' => $validatedOrder['status'] === 'submitted' ? now() : null,
                    'cancelled_at' => $validatedOrder['status'] === 'cancelled' ? now() : null,
                ]);

                foreach ($preparedOrderItems as $preparedOrderItem) {
                    $order->items()->create($preparedOrderItem);
                }

                return $order;
            });

            // Step 4: log the successful order creation for traceability.
            Log::info('Order created successfully.', ['order_id' => $order->id, 'user_id' => $user->id]);

            // Step 5: return the fully loaded order for downstream use.
            return $this->getOrderById($order->id, $user);
        } catch (Throwable $exception) {
            Log::error('Failed to create order.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This updates an existing order and replaces its item rows with the submitted snapshot.
    public function updateOrderById(int $orderId, array $validatedOrder, User $user): Order
    {
        try {
            // Step 1: load the existing order that belongs to the current user.
            $order = $this->getOrderById($orderId, $user);

            // Step 2: prepare all fresh item rows from the submitted form data.
            $preparedOrderItems = $this->prepareOrderItems($validatedOrder, $user);

            // Step 3: recalculate the order totals using the latest item rows.
            $orderTotals = $this->calculateOrderTotals($validatedOrder, $preparedOrderItems);

            // Step 4: update the order header and replace old items inside one transaction.
            DB::transaction(function () use ($order, $validatedOrder, $user, $preparedOrderItems, $orderTotals): void {
                $submittedAt = $order->submitted_at;
                $cancelledAt = $order->cancelled_at;

                if ($validatedOrder['status'] === 'submitted' && ! $submittedAt) {
                    $submittedAt = now();
                }

                if ($validatedOrder['status'] === 'cancelled') {
                    $cancelledAt = now();
                }

                if ($validatedOrder['status'] !== 'cancelled') {
                    $cancelledAt = null;
                }

                $order->update([
                    'company_id' => $user->company_id,
                    'status' => $validatedOrder['status'],
                    'currency' => $orderTotals['currency'],
                    'subtotal_amount' => $orderTotals['subtotal_amount'],
                    'tax_amount' => $orderTotals['tax_amount'],
                    'discount_amount' => $orderTotals['discount_amount'],
                    'shipping_amount' => $orderTotals['shipping_amount'],
                    'adjustment_amount' => $orderTotals['adjustment_amount'],
                    'rounding_amount' => $orderTotals['rounding_amount'],
                    'total_amount' => $orderTotals['total_amount'],
                    'pricing_snapshot' => $orderTotals['pricing_snapshot'],
                    'notes' => $validatedOrder['notes'] ?: null,
                    'submitted_at' => $submittedAt,
                    'cancelled_at' => $cancelledAt,
                ]);

                $order->items()->delete();

                foreach ($preparedOrderItems as $preparedOrderItem) {
                    $order->items()->create($preparedOrderItem);
                }
            });

            // Step 5: log the successful order update for review and debugging.
            Log::info('Order updated successfully.', ['order_id' => $orderId, 'user_id' => $user->id]);

            // Step 6: return the latest saved order with relations.
            return $this->getOrderById($orderId, $user);
        } catch (Throwable $exception) {
            Log::error('Failed to update order.', ['order_id' => $orderId, 'user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This returns one visible order with its relations for the current user.
    public function getOrderById(int $orderId, User $user): Order
    {
        try {
            // Step 1: start with the base order query and eager-load the needed relations.
            $query = Order::query()
                ->with([
                    'placedByUser:id,name,email,user_type',
                    'company:id,name,company_type',
                    'items' => fn ($builder) => $builder->orderBy('sort_order')->orderBy('id'),
                    'items.product:id,name,sku,product_image_id',
                    'items.product.primaryImage:id,file_path',
                    'items.variant:id,product_id,sku,variant_name',
                ]);

            // Step 2: keep the visibility simple by limiting normal users to their own orders.
            if (! $user->isAdmin()) {
                $query->where('placed_by_user_id', $user->id);
            }

            // Step 3: return the order or fail with a clean not-found error.
            return $query->findOrFail($orderId);
        } catch (Throwable $exception) {
            Log::error('Failed to load order by id.', ['order_id' => $orderId, 'user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This softly deletes one visible order so it no longer appears in normal queries.
    public function softDeleteOrderById(int $orderId, User $user): void
    {
        try {
            // Step 1: load the order that the current user is allowed to remove.
            $order = $this->getOrderById($orderId, $user);

            // Step 2: soft delete the order row using Laravel's SoftDeletes support.
            $order->delete();

            // Step 3: log the deletion for audit and support review.
            Log::info('Order soft deleted successfully.', ['order_id' => $orderId, 'user_id' => $user->id]);
        } catch (Throwable $exception) {
            Log::error('Failed to soft delete order.', ['order_id' => $orderId, 'user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This loads visible products and attaches the resolved price fields used by the order form.
    protected function loadVisibleProducts(User $user): Collection
    {
        try {
            // Step 1: load visible products using the existing product visibility rules.
            return $this->dataVisibilityService->visibleProductQuery($user)
                ->orderBy('products.name')
                ->get()
                ->map(function ($product) use ($user) {
                    // Step 2: resolve the current visible price for each product.
                    $price = $this->priceService->resolveProductPrice((int) $product->id, $user);

                    $product->visible_price = $price['amount'] ?? null;
                    $product->visible_currency = $price['currency'] ?? 'INR';
                    $product->visible_price_type = $price['price_type'] ?? null;
                    $product->gst_rate = $price['gst_rate'] ?? 0;
                    $product->tax_amount = $price['tax_amount'] ?? 0;
                    $product->price_after_gst = $price['price_after_gst'] ?? 0;
                    $product->min_order_quantity = $price['min_order_quantity'] ?? 1;
                    $product->max_order_quantity = $price['max_order_quantity'] ?? null;
                    $product->lot_size = $price['lot_size'] ?? 1;
                    $product->product_variant_id = $price['product_variant_id'] ?? null;
                    $product->variant_name = $price['variant_name'] ?? null;
                    $product->variant_sku = $price['variant_sku'] ?? null;

                    return $product;
                });
        } catch (Throwable $exception) {
            Log::error('Failed to load visible products for order.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This returns paginated orders for the current user so the CRUD page can list them.
    protected function listOrdersForUser(User $user): LengthAwarePaginator
    {
        try {
            // Step 1: start with the order query and eager-load minimal list relations.
            $query = Order::query()
                ->with([
                    'company:id,name',
                ])
                ->orderByDesc('created_at');

            // Step 2: keep normal users limited to their own order list.
            if (! $user->isAdmin()) {
                $query->where('placed_by_user_id', $user->id);
            }

            // Step 3: return the paginated list for the view.
            return $query->paginate(10);
        } catch (Throwable $exception) {
            Log::error('Failed to list orders for user.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This prepares all order item rows from the submitted product and quantity arrays.
    protected function prepareOrderItems(array $validatedOrder, User $user): array
    {
        try {
            // Step 1: read all submitted product and quantity rows.
            $productIds = is_array($validatedOrder['product_id'] ?? null) ? $validatedOrder['product_id'] : [];
            $quantities = is_array($validatedOrder['quantity'] ?? null) ? $validatedOrder['quantity'] : [];
            $rowCount = max(count($productIds), count($quantities));
            $preparedItems = [];

            // Step 2: prepare one order item per submitted row.
            for ($index = 0; $index < $rowCount; $index++) {
                $productId = (int) ($productIds[$index] ?? 0);
                $quantity = (int) ($quantities[$index] ?? 0);

                if ($productId === 0) {
                    continue;
                }

                $visibleProduct = $this->dataVisibilityService->visibleProductQuery($user)
                    ->where('products.id', $productId)
                    ->first();

                if (! $visibleProduct) {
                    throw ValidationException::withMessages([
                        "product_id.$index" => 'The selected product is not available for this user.',
                    ]);
                }

                $price = $this->priceService->resolveProductPrice($productId, $user, $quantity);

                if (! $price) {
                    throw ValidationException::withMessages([
                        "product_id.$index" => 'No visible price is configured for the selected product.',
                    ]);
                }

                $this->validateOrderQuantity($quantity, $price, $index);
                $preparedItems[] = $this->buildPreparedOrderItem($visibleProduct, $price, $quantity, $index);
            }

            // Step 3: stop when the user submits an order without valid items.
            if ($preparedItems === []) {
                throw ValidationException::withMessages([
                    'product_id' => 'Add at least one order item.',
                ]);
            }

            return $preparedItems;
        } catch (Throwable $exception) {
            Log::error('Failed to prepare order items.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This calculates the final order totals from items and extra header amounts.
    protected function calculateOrderTotals(array $validatedOrder, array $preparedOrderItems): array
    {
        try {
            // Step 1: read extra amounts from the submitted form.
            $shippingAmount = round((float) ($validatedOrder['shipping_amount'] ?? 0), 4);
            $adjustmentAmount = round((float) ($validatedOrder['adjustment_amount'] ?? 0), 4);
            $roundingAmount = round((float) ($validatedOrder['rounding_amount'] ?? 0), 4);
            $currency = (string) ($preparedOrderItems[0]['item_snapshot']['currency'] ?? 'INR');

            // Step 2: sum all item-level amounts that will become the order totals.
            $subtotalAmount = round(collect($preparedOrderItems)->sum('subtotal_amount'), 4);
            $taxAmount = round(collect($preparedOrderItems)->sum('tax_amount'), 4);
            $discountAmount = round(collect($preparedOrderItems)->sum('discount_amount'), 4);
            $itemsTotal = round(collect($preparedOrderItems)->sum('total_amount'), 4);
            $totalAmount = round($itemsTotal + $shippingAmount + $adjustmentAmount + $roundingAmount, 4);

            // Step 3: keep an easy-to-read snapshot for debugging and future reviewers.
            $pricingSnapshot = [
                'currency' => $currency,
                'items_count' => count($preparedOrderItems),
                'price_source' => 'current_visible_price',
                'subtotal_amount' => $subtotalAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'items_total' => $itemsTotal,
                'shipping_amount' => $shippingAmount,
                'adjustment_amount' => $adjustmentAmount,
                'rounding_amount' => $roundingAmount,
                'total_amount' => $totalAmount,
                'price_types' => collect($preparedOrderItems)
                    ->map(fn (array $item) => $item['item_snapshot']['price_type'] ?? null)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
            ];

            // Step 4: return the final totals array used by create and update flows.
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
        } catch (Throwable $exception) {
            Log::error('Failed to calculate order totals.', ['error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This turns one selected product row into the final order item payload.
    protected function buildPreparedOrderItem(object $visibleProduct, array $price, int $quantity, int $index): array
    {
        try {
            // Step 1: calculate the row totals from the resolved current price.
            $unitPrice = round((float) ($price['amount'] ?? 0), 4);
            $unitBasePrice = round((float) ($price['base_amount'] ?? $unitPrice), 4);
            $unitTaxAmount = round((float) ($price['tax_amount'] ?? 0), 4);
            $unitPriceAfterGst = round((float) ($price['price_after_gst'] ?? 0), 4);
            $subtotalAmount = round($unitPrice * $quantity, 4);
            $taxAmount = round($unitTaxAmount * $quantity, 4);
            $discountAmount = round((float) ($price['discount_amount'] ?? 0) * $quantity, 4);
            $totalAmount = round($unitPriceAfterGst * $quantity, 4);

            // Step 2: keep the extra pricing fields inside the item snapshot.
            $itemSnapshot = [
                'currency' => $price['currency'] ?? 'INR',
                'price_type' => $price['price_type'] ?? null,
                'base_unit_price' => $unitBasePrice,
                'pricing_stage' => $price['pricing_stage'] ?? 'base_price',
                'gst_rate' => round((float) ($price['gst_rate'] ?? 0), 4),
                'unit_tax_amount' => $unitTaxAmount,
                'unit_price_after_gst' => $unitPriceAfterGst,
                'unit_discount_amount' => round((float) ($price['discount_amount'] ?? 0), 4),
                'product_discount_amount' => round((float) ($price['product_discount_amount'] ?? 0), 4),
                'bulk_discount_amount' => round((float) ($price['bulk_discount_amount'] ?? 0), 4),
                'coupon_discount_amount' => round((float) ($price['coupon_discount_amount'] ?? 0), 4),
                'applied_coupon_code' => $price['applied_coupon_code'] ?? null,
                'coupon_status' => $price['coupon_status'] ?? null,
                'coupon_message' => $price['coupon_message'] ?? null,
                'min_order_quantity' => (int) ($price['min_order_quantity'] ?? 1),
                'max_order_quantity' => $price['max_order_quantity'] === null ? null : (int) $price['max_order_quantity'],
                'lot_size' => (int) ($price['lot_size'] ?? 1),
                'product_variant_id' => $price['product_variant_id'] ?? null,
                'variant_sku' => $price['variant_sku'] ?? null,
                'variant_name' => $price['variant_name'] ?? null,
            ];

            // Step 3: return the final order item payload used by create and update.
            return [
                'product_id' => (int) $visibleProduct->id,
                'product_variant_id' => $price['product_variant_id'] ?? null,
                'sku' => (string) ($price['variant_sku'] ?? $visibleProduct->sku),
                'product_name' => (string) $visibleProduct->name,
                'variant_name' => $price['variant_name'] ?? null,
                'description' => 'Resolved from current visible product price.',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal_amount' => $subtotalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'sort_order' => $index,
                'item_snapshot' => $itemSnapshot,
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build order item payload.', ['product_id' => $visibleProduct->id ?? null, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    // This checks the selected quantity against the current sellable variant rules.
    protected function validateOrderQuantity(int $quantity, array $price, int $index): void
    {
        try {
            // Step 1: read min quantity, max quantity, and lot size from the resolved variant rules.
            $minOrderQuantity = max(1, (int) ($price['min_order_quantity'] ?? 1));
            $maxOrderQuantity = $price['max_order_quantity'] === null ? null : (int) $price['max_order_quantity'];
            $lotSize = max(1, (int) ($price['lot_size'] ?? 1));

            // Step 2: block quantities below the configured minimum.
            if ($quantity < $minOrderQuantity) {
                throw ValidationException::withMessages([
                    "quantity.$index" => "Quantity for item ".($index + 1)." must be at least {$minOrderQuantity}.",
                ]);
            }

            // Step 3: block quantities above the configured maximum when a maximum exists.
            if ($maxOrderQuantity !== null && $quantity > $maxOrderQuantity) {
                throw ValidationException::withMessages([
                    "quantity.$index" => "Quantity for item ".($index + 1)." must not exceed {$maxOrderQuantity}.",
                ]);
            }

            // Step 4: block quantities that do not match the configured lot size.
            if ($lotSize > 1 && $quantity % $lotSize !== 0) {
                throw ValidationException::withMessages([
                    "quantity.$index" => "Quantity for item ".($index + 1)." must be in multiples of {$lotSize}.",
                ]);
            }
        } catch (Throwable $exception) {
            Log::error('Failed to validate order quantity.', ['item_index' => $index, 'quantity' => $quantity, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

}
