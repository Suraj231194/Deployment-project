<?php

namespace App\Services\Cart;

use App\Models\Authorization\User;
use App\Models\Cart\Cart;
use App\Models\Cart\CartItem;
use App\Models\Product\ProductVariant;
use App\Services\Pricing\PriceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class CartService
{
    public function __construct(
        protected PriceService $priceService,
    ) {
    }

    // This returns the current shopper cart as a JSON-ready array.
    public function showCurrentCart(Request $request): array
    {
        try {
            // Step 1: read the current shopper details from the request.
            $user = $request->user();
            $guestCartSessionId = $user ? null : $this->resolveGuestCartSessionId($request);

            // Step 2: load the current cart for the active shopper.
            $cart = $this->findCart($user, $guestCartSessionId);

            // Step 3: return the prepared cart payload.
            return $this->buildCartResponse($cart, $user, $guestCartSessionId);
        } catch (Throwable $exception) {
            Log::error('Failed to show current cart.', [
                'user_id' => $request->user()?->id,
                'session_id' => $request->session()->get('guest_cart_session_id'),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This adds one item into the current shopper cart.
    public function addItemToCurrentCart(array $validatedCartItem, Request $request): array
    {
        try {
            // Step 1: read the current shopper details from the request.
            $user = $request->user();
            $guestCartSessionId = $user ? null : $this->resolveGuestCartSessionId($request);

            // Step 2: find or create the current cart row.
            $cart = $this->findOrCreateCart($user, $guestCartSessionId);

            // Step 3: resolve the exact variant that should be stored in the cart.
            $productVariantId = $this->resolveCartVariantId($validatedCartItem, $user);

            // Step 4: load the existing cart item for the same variant.
            $cartItem = $cart->items()
                ->where('product_variant_id', $productVariantId)
                ->first();

            // Step 5: calculate the final quantity after the add action.
            $requestedQuantity = (int) $validatedCartItem['quantity'];
            $existingQuantity = $cartItem ? (int) $cartItem->quantity : 0;
            $finalQuantity = $existingQuantity + $requestedQuantity;

            // Step 6: load the live price for the final quantity.
            $resolvedVariantPrice = $this->priceService->resolveVariantPrice($productVariantId, $user, $finalQuantity);

            if (! $resolvedVariantPrice) {
                $productVariantMessage = $user
                    ? 'The selected product variant is not available for this user.'
                    : 'The selected product variant is not available right now.';

                throw ValidationException::withMessages([
                    'product_variant_id' => $productVariantMessage,
                ]);
            }

            // Step 7: validate the final quantity.
            $this->validateCartQuantity($finalQuantity, $resolvedVariantPrice);

            // Step 8: save the cart item.
            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $finalQuantity,
                ]);
            } else {
                $cart->items()->create([
                    'product_variant_id' => $productVariantId,
                    'quantity' => $finalQuantity,
                ]);
            }

            // Step 9: keep the cart currency aligned with the current price.
            $cart->update([
                'currency' => $resolvedVariantPrice['currency'] ?? 'INR',
            ]);

            // Step 10: load the refreshed cart after save.
            $refreshedCart = $this->findCart($user, $guestCartSessionId);

            // Step 11: return the refreshed cart payload.
            return $this->buildCartResponse($refreshedCart, $user, $guestCartSessionId);
        } catch (Throwable $exception) {
            Log::error('Failed to add item to current cart.', [
                'user_id' => $request->user()?->id,
                'session_id' => $request->session()->get('guest_cart_session_id'),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This updates one item quantity in the current shopper cart.
    public function updateCurrentCartItemQuantity(int $cartItemId, array $validatedCartItem, Request $request): array
    {
        try {
            // Step 1: read the current shopper details from the request.
            $user = $request->user();
            $guestCartSessionId = $user ? null : $this->resolveGuestCartSessionId($request);

            // Step 2: load the selected cart item and confirm ownership.
            $cartItem = $this->findCartItem($cartItemId, $user, $guestCartSessionId);

            // Step 3: load the live price for the requested quantity.
            $updatedQuantity = (int) $validatedCartItem['quantity'];
            $resolvedVariantPrice = $this->priceService->resolveVariantPrice(
                (int) $cartItem->product_variant_id,
                $user,
                $updatedQuantity
            );

            if (! $resolvedVariantPrice) {
                throw ValidationException::withMessages([
                    'cart_item_id' => 'The selected cart item is no longer available for checkout.',
                ]);
            }

            // Step 4: validate the requested quantity.
            $this->validateCartQuantity($updatedQuantity, $resolvedVariantPrice);

            // Step 5: save the new quantity.
            $cartItem->update([
                'quantity' => $updatedQuantity,
            ]);

            // Step 6: keep the cart currency aligned with the current price.
            $cartItem->cart->update([
                'currency' => $resolvedVariantPrice['currency'] ?? 'INR',
            ]);

            // Step 7: load the refreshed cart after update.
            $refreshedCart = $this->findCart($user, $guestCartSessionId);

            // Step 8: return the refreshed cart payload.
            return $this->buildCartResponse($refreshedCart, $user, $guestCartSessionId);
        } catch (Throwable $exception) {
            Log::error('Failed to update current cart item.', [
                'user_id' => $request->user()?->id,
                'session_id' => $request->session()->get('guest_cart_session_id'),
                'cart_item_id' => $cartItemId,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This removes one item from the current shopper cart.
    public function removeItemFromCurrentCart(int $cartItemId, Request $request): array
    {
        try {
            // Step 1: read the current shopper details from the request.
            $user = $request->user();
            $guestCartSessionId = $user ? null : $this->resolveGuestCartSessionId($request);

            // Step 2: load the selected cart item and confirm ownership.
            $cartItem = $this->findCartItem($cartItemId, $user, $guestCartSessionId);

            // Step 3: keep the cart row before deleting the item.
            $cart = $cartItem->cart;

            // Step 4: remove the cart item row.
            $cartItem->delete();

            // Step 5: return an empty cart when no items remain.
            if (! $cart->items()->exists()) {
                $cart->delete();

                return $this->buildCartResponse(null, $user, $guestCartSessionId);
            }

            // Step 6: load the refreshed cart after remove.
            $refreshedCart = $this->findCart($user, $guestCartSessionId);

            // Step 7: return the refreshed cart payload.
            return $this->buildCartResponse($refreshedCart, $user, $guestCartSessionId);
        } catch (Throwable $exception) {
            Log::error('Failed to remove item from current cart.', [
                'user_id' => $request->user()?->id,
                'session_id' => $request->session()->get('guest_cart_session_id'),
                'cart_item_id' => $cartItemId,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This moves guest cart items into the signed-in account cart.
    public function moveGuestCartItemsToUserCart(Request $request, User $user): array
    {
        $cartMoveSummary = [
            'moved_items_count' => 0,
            'skipped_items_count' => 0,
        ];

        try {
            // Step 1: read the saved guest cart session id without creating a new one.
            $guestCartSessionId = $this->readGuestCartSessionId($request);

            if (! $guestCartSessionId) {
                return $cartMoveSummary;
            }

            // Step 2: load the guest cart for the saved browser session.
            $guestCart = $this->findGuestCartBySessionId($guestCartSessionId);

            if (! $guestCart || $guestCart->items->isEmpty()) {
                $this->clearGuestCartSessionId($request);
                return $cartMoveSummary;
            }

            // Step 3: load the existing user cart when it already exists.
            $userCart = $this->findUserCart($user);

            // Step 4: move the items inside one database transaction.
            DB::beginTransaction();

            foreach ($guestCart->items as $guestCartItem) {
                $existingUserCartItem = null;

                if ($userCart) {
                    $existingUserCartItem = $userCart->items()
                        ->where('product_variant_id', $guestCartItem->product_variant_id)
                        ->first();
                }

                // Step 5: calculate the merged quantity for the user cart.
                $guestQuantity = (int) $guestCartItem->quantity;
                $existingQuantity = 0;

                if ($existingUserCartItem) {
                    $existingQuantity = (int) $existingUserCartItem->quantity;
                }

                $finalQuantity = $existingQuantity + $guestQuantity;

                // Step 6: load the live signed-in price for the merged quantity.
                $resolvedVariantPrice = $this->priceService->resolveVariantPrice((int) $guestCartItem->product_variant_id, $user, $finalQuantity);

                if (! $resolvedVariantPrice) {
                    $cartMoveSummary['skipped_items_count']++;
                    continue;
                }

                // Step 7: skip the item when the merged quantity breaks current rules.
                $minimumQuantity = max(1, (int) ($resolvedVariantPrice['min_order_quantity'] ?? 1));
                $maximumQuantity = $resolvedVariantPrice['max_order_quantity'] === null ? null : (int) $resolvedVariantPrice['max_order_quantity'];
                $lotSize = max(1, (int) ($resolvedVariantPrice['lot_size'] ?? 1));

                $quantityBelowMinimum = $finalQuantity < $minimumQuantity;
                $quantityAboveMaximum = $maximumQuantity !== null && $finalQuantity > $maximumQuantity;
                $quantityBreaksLotSize = $lotSize > 1 && $finalQuantity % $lotSize !== 0;

                if ($quantityBelowMinimum || $quantityAboveMaximum || $quantityBreaksLotSize) {
                    $cartMoveSummary['skipped_items_count']++;
                    continue;
                }

                // Step 8: create the user cart only when at least one item can move.
                if (! $userCart) {
                    $userCart = Cart::query()->create([
                        'user_id' => $user->id,
                        'currency' => $resolvedVariantPrice['currency'] ?? 'INR',
                    ]);
                }

                // Step 9: save the moved item into the user cart.
                if ($existingUserCartItem) {
                    $existingUserCartItem->update([
                        'quantity' => $finalQuantity,
                    ]);
                } else {
                    $userCart->items()->create([
                        'product_variant_id' => $guestCartItem->product_variant_id,
                        'quantity' => $finalQuantity,
                    ]);
                }

                // Step 10: keep the user cart currency aligned.
                $userCart->update([
                    'currency' => $resolvedVariantPrice['currency'] ?? 'INR',
                ]);

                $cartMoveSummary['moved_items_count']++;
            }

            // Step 11: remove the old guest cart after the move is complete.
            $guestCart->items()->delete();
            $guestCart->delete();

            DB::commit();

            // Step 12: clear the saved guest cart session after the move.
            $this->clearGuestCartSessionId($request);

            return $cartMoveSummary;
        } catch (Throwable $exception) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Failed to move guest cart into user cart.', [
                'user_id' => $user->id,
                'session_id' => $request->session()->get('guest_cart_session_id'),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This loads one cart for the current shopper identity.
    protected function findCart(?User $user, ?string $guestCartSessionId): ?Cart
    {
        // Step 1: start the shared cart query with the relations used by the storefront.
        $cartQuery = Cart::query()->with([
            'items' => fn ($builder) => $builder->orderBy('id'),
            'items.variant.product.primaryImage',
        ]);

        // Step 2: limit the cart query to the signed-in shopper or the guest session.
        if ($user) {
            $cartQuery->where('user_id', $user->id);
        } else {
            $cartQuery->where('session_id', $guestCartSessionId);
        }

        // Step 3: return the first matching cart.
        return $cartQuery->first();
    }

    // This finds or creates one cart row for the current shopper identity.
    protected function findOrCreateCart(?User $user, ?string $guestCartSessionId): Cart
    {
        // Step 1: create the user cart only when one does not already exist.
        if ($user) {
            return Cart::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['currency' => 'INR'],
            );
        }

        // Step 2: create the guest cart only when one does not already exist.
        return Cart::query()->firstOrCreate(
            ['session_id' => $guestCartSessionId],
            ['currency' => 'INR'],
        );
    }

    // This loads one cart item and confirms it belongs to the current shopper.
    protected function findCartItem(int $cartItemId, ?User $user, ?string $guestCartSessionId): CartItem
    {
        // Step 1: start the shared cart item query with the relations used by the cart UI.
        $cartItemQuery = CartItem::query()
            ->with(['cart', 'variant.product'])
            ->whereKey($cartItemId);

        // Step 2: limit the cart item query to the signed-in shopper or the guest session.
        if ($user) {
            $cartItemQuery->whereHas('cart', function ($builder) use ($user): void {
                $builder->where('user_id', $user->id);
            });
        } else {
            $cartItemQuery->whereHas('cart', function ($builder) use ($guestCartSessionId): void {
                $builder->where('session_id', $guestCartSessionId);
            });
        }

        // Step 3: load the owned cart item.
        $cartItem = $cartItemQuery->first();

        if (! $cartItem) {
            throw (new ModelNotFoundException())->setModel(CartItem::class, [$cartItemId]);
        }

        // Step 4: return the owned cart item row.
        return $cartItem;
    }

    // This resolves which product variant should be stored in the current cart.
    protected function resolveCartVariantId(array $validatedCartItem, ?User $user): int
    {
        // Step 1: use the provided product variant when the client sends one.
        if (! empty($validatedCartItem['product_variant_id'])) {
            $selectedVariant = ProductVariant::query()
                ->whereKey((int) $validatedCartItem['product_variant_id'])
                ->where('product_id', (int) $validatedCartItem['product_id'])
                ->where('is_active', true)
                ->first();

            if (! $selectedVariant) {
                throw ValidationException::withMessages([
                    'product_variant_id' => 'The selected product variant does not belong to the selected product.',
                ]);
            }

            return (int) $selectedVariant->id;
        }

        // Step 2: fall back to the first visible priced variant for the selected product.
        $resolvedProductPrice = $this->priceService->resolveProductPrice((int) $validatedCartItem['product_id'], $user);

        if (! $resolvedProductPrice) {
            $productMessage = 'The selected product is not available right now.';

            if ($user) {
                $productMessage = 'The selected product is not available for this user.';
            }

            throw ValidationException::withMessages([
                'product_id' => $productMessage,
            ]);
        }

        // Step 3: return the resolved default variant id.
        return (int) ($resolvedProductPrice['product_variant_id'] ?? 0);
    }

    // This validates cart quantity using the live current variant rules.
    protected function validateCartQuantity(int $quantity, array $resolvedVariantPrice): void
    {
        // Step 1: read the live min quantity, max quantity, and lot size.
        $minimumQuantity = max(1, (int) ($resolvedVariantPrice['min_order_quantity'] ?? 1));
        $maximumQuantity = $resolvedVariantPrice['max_order_quantity'] === null ? null : (int) $resolvedVariantPrice['max_order_quantity'];
        $lotSize = max(1, (int) ($resolvedVariantPrice['lot_size'] ?? 1));

        // Step 2: block quantities below the configured minimum.
        if ($quantity < $minimumQuantity) {
            throw ValidationException::withMessages([
                'quantity' => "Quantity must be at least {$minimumQuantity}.",
            ]);
        }

        // Step 3: block quantities above the configured maximum when one exists.
        if ($maximumQuantity !== null && $quantity > $maximumQuantity) {
            throw ValidationException::withMessages([
                'quantity' => "Quantity must not exceed {$maximumQuantity}.",
            ]);
        }

        // Step 4: block quantities that do not match the configured lot size.
        if ($lotSize > 1 && $quantity % $lotSize !== 0) {
            throw ValidationException::withMessages([
                'quantity' => "Quantity must be in multiples of {$lotSize}.",
            ]);
        }
    }

    // This builds the full cart payload for both signed-in and guest shoppers.
    protected function buildCartResponse(?Cart $cart, ?User $user, ?string $guestCartSessionId): array
    {
        // Step 1: return a clean empty cart structure when no cart exists.
        if (! $cart) {
            $emptyCart = [
                'id' => null,
                'user_id' => null,
                'currency' => 'INR',
                'items_count' => 0,
                'subtotal_amount' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'items' => [],
            ];

            if ($user) {
                $emptyCart['user_id'] = $user->id;
                return $emptyCart;
            }

            $emptyCart['session_id'] = $guestCartSessionId;

            return $emptyCart;
        }

        // Step 2: build the current cart item payloads from all saved item rows.
        $cartItems = [];

        foreach ($cart->items as $cartItem) {
            $cartItems[] = $this->buildCartItemResponse($cartItem, $user);
        }

        // Step 3: calculate current totals from the built cart items.
        $cartTotals = $this->calculateCartTotals($cartItems);

        // Step 4: prepare the final cart response array.
        $cartResponse = [
            'id' => $cart->id,
            'user_id' => $cart->user_id,
            'currency' => $cartTotals['currency'],
            'items_count' => count($cartItems),
            'subtotal_amount' => $cartTotals['subtotal_amount'],
            'tax_amount' => $cartTotals['tax_amount'],
            'total_amount' => $cartTotals['total_amount'],
            'items' => $cartItems,
        ];

        // Step 5: keep the guest session id only on guest cart responses.
        if (! $user) {
            $cartResponse['session_id'] = $cart->session_id ?: $guestCartSessionId;
        }

        // Step 6: return the final cart response.
        return $cartResponse;
    }

    // This builds one cart item payload with live price values.
    protected function buildCartItemResponse(CartItem $cartItem, ?User $user): array
    {
        // Step 1: load the product, variant, and primary image used across the cart UI.
        $productVariant = $cartItem->variant;
        $product = $productVariant?->product;
        $imagePath = $product?->primaryImage?->file_path;

        // Step 2: resolve the live current price for the selected variant.
        $resolvedVariantPrice = $this->priceService->resolveVariantPrice((int) $cartItem->product_variant_id, $user, (int) $cartItem->quantity);

        if (! $resolvedVariantPrice) {
            throw ValidationException::withMessages([
                'cart_item_id' => 'One cart item is no longer available with current pricing.',
            ]);
        }

        // Step 3: calculate the cart line totals from the live price.
        $unitPrice = round((float) ($resolvedVariantPrice['amount'] ?? 0), 4);
        $unitTaxAmount = round((float) ($resolvedVariantPrice['tax_amount'] ?? 0), 4);
        $unitPriceAfterGst = round((float) ($resolvedVariantPrice['price_after_gst'] ?? 0), 4);
        $lineSubtotal = round($unitPrice * (int) $cartItem->quantity, 4);
        $lineTaxAmount = round($unitTaxAmount * (int) $cartItem->quantity, 4);
        $lineTotal = round($unitPriceAfterGst * (int) $cartItem->quantity, 4);

        // Step 4: return a cart item payload that the storefront can render directly.
        return [
            'id' => $cartItem->id,
            'product_id' => $product?->id,
            'product_variant_id' => $cartItem->product_variant_id,
            'product_name' => $product?->name,
            'variant_name' => $productVariant?->variant_name,
            'sku' => $productVariant?->sku,
            'image_url' => filled($imagePath) ? asset($imagePath) : null,
            'quantity' => (int) $cartItem->quantity,
            'currency' => $resolvedVariantPrice['currency'] ?? 'INR',
            'price_type' => $resolvedVariantPrice['price_type'] ?? null,
            'unit_price' => $unitPrice,
            'base_unit_price' => round((float) ($resolvedVariantPrice['base_amount'] ?? $unitPrice), 4),
            'gst_rate' => round((float) ($resolvedVariantPrice['gst_rate'] ?? 0), 4),
            'tax_amount' => $lineTaxAmount,
            'unit_price_after_gst' => $unitPriceAfterGst,
            'line_subtotal' => $lineSubtotal,
            'line_total' => $lineTotal,
            'discount_amount' => round((float) ($resolvedVariantPrice['discount_amount'] ?? 0) * (int) $cartItem->quantity, 4),
            'min_order_quantity' => (int) ($resolvedVariantPrice['min_order_quantity'] ?? 1),
            'max_order_quantity' => $resolvedVariantPrice['max_order_quantity'] === null ? null : (int) $resolvedVariantPrice['max_order_quantity'],
            'lot_size' => (int) ($resolvedVariantPrice['lot_size'] ?? 1),
        ];
    }

    // This calculates cart totals from all current cart item payloads.
    protected function calculateCartTotals(array $cartItems): array
    {
        // Step 1: use INR when the cart currently has no items.
        if ($cartItems === []) {
            return [
                'currency' => 'INR',
                'subtotal_amount' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
            ];
        }

        // Step 2: sum all line values from the built cart item payloads.
        $currency = (string) ($cartItems[0]['currency'] ?? 'INR');
        $subtotalAmount = 0;
        $taxAmount = 0;
        $totalAmount = 0;

        foreach ($cartItems as $cartItem) {
            $subtotalAmount += (float) ($cartItem['line_subtotal'] ?? 0);
            $taxAmount += (float) ($cartItem['tax_amount'] ?? 0);
            $totalAmount += (float) ($cartItem['line_total'] ?? 0);
        }

        // Step 3: return the calculated cart totals.
        return [
            'currency' => $currency,
            'subtotal_amount' => round($subtotalAmount, 4),
            'tax_amount' => round($taxAmount, 4),
            'total_amount' => round($totalAmount, 4),
        ];
    }

    // This loads the current user's cart with relations.
    public function findUserCart(User $user): ?Cart
    {
        // Step 1: load the signed-in shopper cart from the shared cart flow.
        return $this->findCart($user, null);
    }

    // This validates cart quantity using the live current variant rules.
    public function validateUserCartQuantity(int $quantity, array $resolvedVariantPrice): void
    {
        // Step 1: validate the requested quantity with the shared cart rules.
        $this->validateCartQuantity($quantity, $resolvedVariantPrice);
    }

    // This reads the saved guest cart session id when one already exists.
    protected function readGuestCartSessionId(Request $request): ?string
    {
        $guestCartSessionId = (string) $request->session()->get('guest_cart_session_id', '');

        if ($guestCartSessionId === '') {
            return null;
        }

        return $guestCartSessionId;
    }

    // This resolves one stable guest cart session id for the current browser session.
    protected function resolveGuestCartSessionId(Request $request): string
    {
        $guestCartSessionId = $this->readGuestCartSessionId($request);

        if ($guestCartSessionId) {
            return $guestCartSessionId;
        }

        $guestCartSessionId = (string) $request->session()->getId();
        $request->session()->put('guest_cart_session_id', $guestCartSessionId);

        return $guestCartSessionId;
    }

    // This clears the saved guest cart session id after the move is complete.
    protected function clearGuestCartSessionId(Request $request): void
    {
        $request->session()->forget('guest_cart_session_id');
    }

    // This loads one guest cart by the saved session id.
    protected function findGuestCartBySessionId(string $guestCartSessionId): ?Cart
    {
        // Step 1: load the guest shopper cart from the shared cart flow.
        return $this->findCart(null, $guestCartSessionId);
    }
}
