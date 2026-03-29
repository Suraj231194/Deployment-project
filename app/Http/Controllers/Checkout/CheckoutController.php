<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Services\Cart\CartService;
use App\Services\Checkout\CheckoutService;
use App\Services\Order\OrderService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class CheckoutController extends Controller
{
    // This shows the checkout page using the checkout-only controller flow.
    public function showCustomerCheckoutPage(Request $request, CheckoutService $checkoutService): View
    {
        try {
            // Step 1: load the cart, saved addresses, and invoice details for checkout.
            $checkoutPageData = $checkoutService->loadCheckoutPageData($request);

            // Step 2: return the existing checkout page with the loaded data.
            return view('checkout', $checkoutPageData);
        } catch (Throwable $exception) {
            // Step 3: log the page load failure and keep the checkout page usable.
            Log::error('Failed to load checkout page.', [
                'user_id' => $request->user()?->id,
                'session_id' => $request->session()->get('guest_cart_session_id'),
                'error' => $exception->getMessage(),
            ]);

            // Step 4: return the checkout page with safe fallback data.
            return view('checkout', [
                'initialCart' => null,
                'savedAddresses' => collect(),
                'checkoutBusinessDetails' => [
                    'show_business_fields' => false,
                    'gstin' => null,
                    'pan_number' => null,
                    'registered_business_name' => null,
                ],
            ]);
        }
    }

    // This starts the buy-now checkout by first adding the selected item into the current cart.
    public function startCheckoutFromBuyNow(Request $request, CartService $cartService): RedirectResponse
    {
        try {
            // Step 1: validate the selected product details coming from the buy-now action.
            $validatedSelectedItem = $request->validate([
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
                'quantity' => ['required', 'integer', 'min:1'],
            ]);

            // Step 2: add the selected item into the current cart.
            $cartService->addItemToCurrentCart($validatedSelectedItem, $request);

            // Step 3: send the customer to the existing checkout page.
            return redirect()->route('checkout.page');
        } catch (Throwable $exception) {
            // Step 4: log the checkout start failure.
            Log::error('Failed to start immediate checkout.', [
                'user_id' => $request->user()?->id,
                'session_id' => $request->session()->get('guest_cart_session_id'),
                'error' => $exception->getMessage(),
            ]);

            // Step 5: send the customer back with a clear checkout error.
            return $this->redirectBackWithCheckoutMessage($exception, 'Unable to start checkout right now.');
        }
    }

    // This submits the checkout page and creates the order from the current cart.
    public function submitUserCheckoutOrder(Request $request, CheckoutService $checkoutService): RedirectResponse
    {
        try {
            // Step 1: keep the current user id ready for address ownership validation.
            $userId = (int) $request->user()->id;

            // Step 2: validate the checkout form fields.
            $validatedCheckout = $request->validate([
                'coupon_code' => ['nullable', 'string', 'max:50'],
                'selected_address_source' => ['required', 'string', Rule::in(['existing', 'new'])],
                'selected_user_address_id' => [
                    'nullable',
                    'integer',
                    'required_if:selected_address_source,existing',
                    Rule::exists('user_address', 'id')->where('user_id', $userId),
                ],
                'new_address_label' => ['nullable', 'string', 'max:255', 'required_if:selected_address_source,new'],
                'new_address_line1' => ['nullable', 'string', 'max:255', 'required_if:selected_address_source,new'],
                'new_address_city' => ['nullable', 'string', 'max:128', 'required_if:selected_address_source,new'],
                'new_address_state' => ['nullable', 'string', 'max:128', 'required_if:selected_address_source,new'],
                'new_address_postal_code' => ['nullable', 'string', 'max:20', 'required_if:selected_address_source,new'],
                'new_address_country' => ['nullable', 'string', 'max:128'],
                'new_address_phone' => ['nullable', 'string', 'max:32'],
                'gstin' => ['nullable', 'string', 'max:20'],
                'pan_number' => ['nullable', 'string', 'max:20'],
                'registered_business_name' => ['nullable', 'string', 'max:255'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            // Step 3: place the order from the current cart.
            $submittedOrder = $checkoutService->placeOrderFromCart($validatedCheckout, $request->user());

            // Step 4: send the customer to the existing confirmation page.
            return redirect()
                ->route('order.confirmation')
                ->with('recentCheckoutOrder', $submittedOrder['order'])
                ->with('success', 'Order placed successfully.');
        } catch (Throwable $exception) {
            // Step 5: log the checkout submit failure.
            Log::error('Failed to submit customer checkout order.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            // Step 6: send the customer back with a clear checkout error.
            return $this->redirectBackWithCheckoutMessage($exception, 'Unable to place your order right now.');
        }
    }

    // This validates the entered coupon and returns the live checkout summary as JSON.
    public function validateCheckoutCoupon(Request $request, CheckoutService $checkoutService, OrderService $orderService): JsonResponse
    {
        try {
            // Step 1: validate the coupon preview request fields.
            $validatedCouponInput = $request->validate([
                'coupon_code' => ['nullable', 'string', 'max:50'],
                'is_reorder_checkout' => ['nullable', 'boolean'],
                'reorder_items' => ['nullable', 'string'],
            ]);

            // Step 2: clean the entered coupon code once.
            $couponCode = trim((string) ($validatedCouponInput['coupon_code'] ?? ''));

            // Step 3: stop early when no coupon code was entered.
            if ($couponCode === '') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please enter a coupon code.',
                    'errors' => [
                        'coupon_code' => ['Please enter a coupon code.'],
                    ],
                ], 422);
            }

            // Step 4: decide which checkout flow should prepare the live coupon preview.
            $isReOrderCheckout = (bool) ($validatedCouponInput['is_reorder_checkout'] ?? false);

            if ($isReOrderCheckout) {
                $couponPreview = $orderService->previewReOrderCoupon($validatedCouponInput, $request->user());
            } else {
                $couponPreview = $checkoutService->previewCheckoutCoupon($validatedCouponInput, $request->user());
            }

            // Step 5: return the final coupon preview payload.
            return response()->json([
                'status' => 'success',
                'message' => $couponPreview['coupon_message'] ?? 'Coupon applied successfully.',
                'coupon_preview' => $couponPreview,
            ]);
        } catch (Throwable $exception) {
            // Step 6: log the coupon preview failure for later review.
            Log::error('Failed to validate checkout coupon.', [
                'user_id' => $request->user()?->id,
                'coupon_code' => $request->input('coupon_code'),
                'error' => $exception->getMessage(),
            ]);

            // Step 7: return a clean JSON error response.
            return $this->buildJsonErrorResponse($exception, 'Unable to validate coupon right now.');
        }
    }

    // This refreshes reorder checkout item pricing when quantity changes on the page.
    public function previewReOrderPricing(Request $request, OrderService $orderService): JsonResponse
    {
        try {
            // Step 1: validate the posted reorder pricing fields.
            $validatedPreviewInput = $request->validate([
                'coupon_code' => ['nullable', 'string', 'max:50'],
                'reorder_items' => ['required', 'string'],
            ]);

            // Step 2: prepare the refreshed reorder pricing from the latest quantities.
            $reOrderPreview = $orderService->previewReOrderPricing($validatedPreviewInput, $request->user());

            // Step 3: return the refreshed reorder items and coupon preview.
            return response()->json([
                'status' => 'success',
                'message' => 'Checkout pricing refreshed successfully.',
                'reorder_preview' => $reOrderPreview,
            ]);
        } catch (Throwable $exception) {
            // Step 4: log the reorder pricing preview failure.
            Log::error('Failed to refresh reorder checkout pricing.', [
                'user_id' => $request->user()?->id,
                'coupon_code' => $request->input('coupon_code'),
                'error' => $exception->getMessage(),
            ]);

            // Step 5: return a clean JSON error response.
            return $this->buildJsonErrorResponse($exception, 'Unable to refresh checkout pricing right now.');
        }
    }

    // This checks out the current cart from the existing JSON route.
    public function submitUserCartCheckout(Request $request, CheckoutService $checkoutService): JsonResponse
    {
        try {
            // Step 1: validate the optional extra checkout amounts and notes.
            $validatedCheckout = $request->validate([
                'shipping_amount' => ['nullable', 'numeric', 'min:0'],
                'adjustment_amount' => ['nullable', 'numeric'],
                'rounding_amount' => ['nullable', 'numeric'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            // Step 2: place the order from the current cart.
            $checkoutResult = $checkoutService->placeOrderFromCart($validatedCheckout, $request->user());

            // Step 3: return the created order summary as JSON.
            return response()->json([
                'status' => 'success',
                'message' => 'Cart checked out successfully.',
                'order' => $checkoutResult['order'],
            ], 201);
        } catch (Throwable $exception) {
            // Step 4: log the JSON checkout failure.
            Log::error('Failed to return checkout-cart JSON response.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            // Step 5: return a clean JSON error response.
            return $this->buildJsonErrorResponse($exception, 'Unable to checkout cart.');
        }
    }

    // This converts known checkout exceptions into a JSON error response.
    protected function buildJsonErrorResponse(Throwable $exception, string $defaultMessage): JsonResponse
    {
        // Step 1: choose the correct HTTP status code for the current exception.
        $statusCode = 500;

        if ($exception instanceof ValidationException || $exception instanceof QueryException) {
            $statusCode = 422;
        }

        if ($exception instanceof AuthorizationException) {
            $statusCode = 403;
        }

        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
            $statusCode = 404;
        }

        // Step 2: return the JSON error payload with optional validation errors.
        return response()->json([
            'status' => 'error',
            'message' => $this->resolveErrorMessage($exception, $defaultMessage),
            'errors' => $exception instanceof ValidationException ? $exception->errors() : [],
        ], $statusCode);
    }

    // This converts checkout failures into a simple redirect response for page-based flows.
    protected function redirectBackWithCheckoutMessage(Throwable $exception, string $defaultMessage): RedirectResponse
    {
        // Step 1: keep field errors on the page when validation fails.
        if ($exception instanceof ValidationException) {
            return back()->withErrors($exception->errors())->withInput();
        }

        // Step 2: return one clear fallback message for unexpected checkout issues.
        return back()->with('error', $defaultMessage);
    }
}
