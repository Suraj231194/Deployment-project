<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Services\Order\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    // This renders the customer profile orders page with real backend data.
    public function showCustomerOrdersPage(Request $request, OrderService $orderService): View
    {
        try {
            // Step 1: load the signed-in user's profile order page data.
            $pageData = $orderService->customerOrdersPageData($request->user());

            // Step 2: return the profile orders screen with backend data.
            return view('userProfile.orders.index', $pageData);
        } catch (Throwable $exception) {
            Log::error('Failed to load customer profile orders page.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            // Step 3: return the same page with safe fallback data.
            return $this->viewWithError('userProfile.orders.index', [
                'orders' => [],
            ], $exception, 'Unable to load your orders right now.');
        }
    }

    // This renders the order CRUD page with create form, edit form, and the user's order list.
    public function showOrderCrud(Request $request, OrderService $orderService): View
    {
        try {
            // Step 1: load all order page data for the logged-in user.
            return view('order.OrderCrud', $orderService->orderCrudPageData(
                $request->user(),
                $request->integer('edit_order_id'),
            ));
        } catch (Throwable $exception) {
            Log::error('Failed to load order CRUD page.', ['user_id' => $request->user()?->id, 'error' => $exception->getMessage()]);

            // Step 2: return the same page with a simple page-level error message.
            return $this->viewWithError('order.OrderCrud', [
                'products' => collect(),
                'orders' => new LengthAwarePaginator([], 0, 10),
                'editingOrder' => null,
                'editingItems' => collect(),
            ], $exception, 'Unable to load order page.');
        }
    }

    // This validates the submitted form and creates a new order for the logged-in user.
    public function createOrder(Request $request, OrderService $orderService): RedirectResponse
    {
        try {
            // Step 1: validate the submitted order form.
            $validatedOrder = $this->validateOrderRequest($request);

            // Step 2: create the order using the order service.
            $order = $orderService->createOrder($validatedOrder, $request->user());

            // Step 3: return to the profile orders page with a success message.
            return redirect()->route('orders.index')
                ->with('success', 'Order #'.$order->id.' created successfully.');
        } catch (Throwable $exception) {
            Log::error('Failed to create order from controller.', ['user_id' => $request->user()?->id, 'error' => $exception->getMessage()]);

            // Step 4: send validation and data errors back to the same form.
            return $this->redirectBackWithError($exception, 'Unable to create order.');
        }
    }

    // This validates the submitted form and updates one existing order by id.
    public function editOrderById(int $orderId, Request $request, OrderService $orderService): RedirectResponse
    {
        try {
            // Step 1: validate the submitted edit form.
            $validatedOrder = $this->validateOrderRequest($request);

            // Step 2: update the existing order and item rows.
            $order = $orderService->updateOrderById($orderId, $validatedOrder, $request->user());

            // Step 3: redirect to the order detail page after a successful update.
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order #'.$order->id.' updated successfully.');
        } catch (Throwable $exception) {
            Log::error('Failed to update order from controller.', ['order_id' => $orderId, 'user_id' => $request->user()?->id, 'error' => $exception->getMessage()]);

            // Step 4: return the user back with a clear error message.
            return $this->redirectBackWithError($exception, 'Unable to update order.');
        }
    }

    // This loads one order by id and shows the order detail page.
    public function getOrderById(int $orderId, Request $request, OrderService $orderService): View
    {
        try {
            // Step 1: load the requested order with relations for the current user.
            $order = $orderService->getOrderById($orderId, $request->user());

            // Step 2: return partial view for AJAX requests, otherwise full detail page.
            if ($request->ajax()) {
                return view('order.partials.order-details', [
                    'order' => $order,
                ]);
            }

            return view('order.show', [
                'order' => $order,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load order detail page.', ['order_id' => $orderId, 'user_id' => $request->user()?->id, 'error' => $exception->getMessage()]);

            // Step 3: return the detail page with a clean page-level error.
            return $this->viewWithError('order.show', [
                'order' => null,
            ], $exception, 'Unable to load order details.');
        }
    }

    // This softly deletes one order by id for the current logged-in user.
    public function softDeleteOrderById(int $orderId, Request $request, OrderService $orderService): RedirectResponse
    {
        try {
            // Step 1: soft delete the selected order.
            $orderService->softDeleteOrderById($orderId, $request->user());

            // Step 2: redirect to the list page with a success message.
            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully.');
        } catch (Throwable $exception) {
            Log::error('Failed to soft delete order from controller.', ['order_id' => $orderId, 'user_id' => $request->user()?->id, 'error' => $exception->getMessage()]);

            // Step 3: return back with a clean user-facing error message.
            return $this->redirectBackWithError($exception, 'Unable to delete order.');
        }
    }

    // This starts the reorder checkout flow for one existing order.
    public function ReOrder(int $orderId, Request $request, OrderService $orderService): RedirectResponse
    {
        try {
            // Step 1: prepare the reorder checkout items from the selected order.
            $orderService->ReOrder($orderId, $request->user(), $request);

            // Step 2: send the customer to the separate reorder checkout page.
            return redirect()->route('orders.reorder.checkout');
        } catch (Throwable $exception) {
            Log::error('Failed to start reorder checkout flow.', [
                'order_id' => $orderId,
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            // Step 3: return back with a clear error message.
            return $this->redirectBackWithError($exception, 'Unable to start reorder right now.');
        }
    }

    // This shows the separate reorder checkout page without using the cart flow.
    public function showReOrderCheckoutPage(Request $request): View|RedirectResponse
    {
        try {
            // Step 1: load the prepared reorder checkout data from session.
            $reOrderCheckout = $request->session()->get('reorder_checkout');

            if (! is_array($reOrderCheckout)) {
                return redirect()->route('customer.orders.preview')
                    ->withErrors([
                        'form' => 'Reorder items are not available right now.',
                    ]);
            }

            // Step 2: keep the latest posted reorder items after validation errors.
            $oldReOrderItems = $request->session()->getOldInput('reorder_items');

            if (is_string($oldReOrderItems) && $oldReOrderItems !== '') {
                $decodedOldReOrderItems = json_decode($oldReOrderItems, true);

                if (is_array($decodedOldReOrderItems)) {
                    $reOrderCheckout['items'] = $decodedOldReOrderItems;
                }
            }

            // Step 3: load the saved addresses for the current user.
            $savedAddresses = collect();

            if ($request->user()) {
                $savedAddresses = $request->user()
                    ->addresses()
                    ->orderByDesc('is_default_shipping')
                    ->orderByDesc('is_default_billing')
                    ->orderByDesc('id')
                    ->get();
            }

            // Step 4: prepare the business invoice details for B2B users.
            $checkoutBusinessDetails = [
                'show_business_fields' => false,
                'gstin' => null,
                'pan_number' => null,
                'registered_business_name' => null,
            ];

            if ($request->user() && $request->user()->isB2b()) {
                $company = $request->user()->company;

                $checkoutBusinessDetails = [
                    'show_business_fields' => true,
                    'gstin' => $company?->gst_number,
                    'pan_number' => $company?->pan_number,
                    'registered_business_name' => $company?->legal_name ?: $company?->name,
                ];
            }

            // Step 5: return the same checkout UI with reorder-only data.
            return view('checkout', [
                'initialCart' => null,
                'isReOrderCheckout' => true,
                'reOrderCheckout' => $reOrderCheckout,
                'savedAddresses' => $savedAddresses,
                'checkoutBusinessDetails' => $checkoutBusinessDetails,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load reorder checkout page.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            // Step 6: return the user to the orders page with a clear error.
            return redirect()->route('customer.orders.preview')
                ->withErrors([
                    'form' => 'Unable to load reorder checkout right now.',
                ]);
        }
    }

    // This submits the separate reorder checkout page without using the cart flow.
    public function submitReOrderCheckout(Request $request, OrderService $orderService): RedirectResponse
    {
        try {
            // Step 1: validate the reorder checkout form fields.
            $validatedCheckout = $request->validate([
                'reorder_items' => ['required', 'string'],
                'coupon_code' => ['nullable', 'string', 'max:50'],
                'selected_address_source' => ['required', 'string', 'in:existing,new'],
                'selected_user_address_id' => ['nullable', 'integer'],
                'new_address_label' => ['nullable', 'string', 'max:255'],
                'new_address_line1' => ['nullable', 'string', 'max:255'],
                'new_address_city' => ['nullable', 'string', 'max:128'],
                'new_address_state' => ['nullable', 'string', 'max:128'],
                'new_address_postal_code' => ['nullable', 'string', 'max:20'],
                'new_address_country' => ['nullable', 'string', 'max:128'],
                'new_address_phone' => ['nullable', 'string', 'max:32'],
                'gstin' => ['nullable', 'string', 'max:20'],
                'pan_number' => ['nullable', 'string', 'max:20'],
                'registered_business_name' => ['nullable', 'string', 'max:255'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            // Step 2: create the final reorder order from the latest live pricing.
            $submittedOrder = $orderService->submitReOrderCheckout($validatedCheckout, $request->user());

            // Step 3: clear the temporary reorder session data after success.
            $request->session()->forget('reorder_checkout');

            // Step 4: send the customer to confirmation with the new order summary.
            return redirect()
                ->route('order.confirmation')
                ->with('recentCheckoutOrder', $submittedOrder['order'])
                ->with('success', 'Order placed successfully.');
        } catch (Throwable $exception) {
            Log::error('Failed to submit reorder checkout.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            // Step 5: return back with a clear user-facing error message.
            return $this->redirectBackWithError($exception, 'Unable to place reorder right now.');
        }
    }

    // This validates the shared order form for both create and update flows.
    protected function validateOrderRequest(Request $request): array
    {
        try {
            // Step 1: validate order header and item array fields.
            return $request->validate([
                'status' => ['required', 'in:draft,submitted,cancelled'],
                'product_id' => ['required', 'array', 'min:1'],
                'product_id.*' => ['nullable', 'integer', 'exists:products,id'],
                'quantity' => ['required', 'array', 'min:1'],
                'quantity.*' => ['nullable', 'integer', 'min:1'],
                'shipping_amount' => ['nullable', 'numeric', 'min:0'],
                'adjustment_amount' => ['nullable', 'numeric'],
                'rounding_amount' => ['nullable', 'numeric'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to validate order request.', ['user_id' => $request->user()?->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}
