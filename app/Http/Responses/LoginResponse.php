<?php

namespace App\Http\Responses;

use App\Services\Cart\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Throwable;

class LoginResponse implements LoginResponseContract
{
    public function __construct(
        protected CartService $cartService,
    ) {
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $cartMoveSummary = [
            'moved_items_count' => 0,
            'skipped_items_count' => 0,
        ];

        // Step 1: move the guest cart into the signed-in account before redirecting the shopper.
        if ($request instanceof Request && $request->user()) {
            try {
                $cartMoveSummary = $this->cartService->moveGuestCartItemsToUserCart($request, $request->user());
            } catch (Throwable $exception) {
                Log::error('Failed to move guest cart after login.', [
                    'user_id' => $request->user()?->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        // Step 2: keep the standard JSON login response unchanged.
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $redirectResponse = redirect()->intended(Fortify::redirects('login'));

        // Step 3: tell the shopper when some guest lines could not be moved after login.
        if (($cartMoveSummary['skipped_items_count'] ?? 0) > 0) {
            $redirectResponse->with('error', 'Some guest cart items could not be moved to your account.');
        }

        return $redirectResponse;
    }
}
