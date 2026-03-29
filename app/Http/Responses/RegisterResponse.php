<?php

namespace App\Http\Responses;

use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;
use Throwable;

class RegisterResponse implements RegisterResponseContract
{
    public function __construct(
        protected CartService $cartService,
    ) {
    }

    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request)
    {
        $cartMoveSummary = [
            'moved_items_count' => 0,
            'skipped_items_count' => 0,
        ];

        // Step 1: move the guest cart into the new account before any redirect or logout happens.
        if ($request instanceof Request && $request->user()) {
            try {
                $cartMoveSummary = $this->cartService->moveGuestCartItemsToUserCart($request, $request->user());
            } catch (Throwable $exception) {
                Log::error('Failed to move guest cart after signup.', [
                    'user_id' => $request->user()?->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $cartMoveWarning = null;

        // Step 2: prepare one warning message when some guest items could not be moved.
        if (($cartMoveSummary['skipped_items_count'] ?? 0) > 0) {
            $cartMoveWarning = 'Some guest cart items could not be moved to your account.';
        }

        // Step 3: keep the pending-approval logout flow unchanged for B2B signup.
        if ($request instanceof Request && $request->user()?->status === 'pending_approval') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $redirectResponse = redirect()
                ->route('login')
                ->with('status', 'Registration submitted. Your B2B account is pending admin approval.');

            if ($cartMoveWarning) {
                $redirectResponse->with('error', $cartMoveWarning);
            }

            return $redirectResponse;
        }

        // Step 4: keep the standard register response for JSON and browser requests.
        if ($request->wantsJson()) {
            return new JsonResponse('', 201);
        }

        $redirectResponse = redirect()->intended(Fortify::redirects('register'));

        // Step 5: attach the cart warning when some guest lines were skipped during signup.
        if ($cartMoveWarning) {
            $redirectResponse->with('error', $cartMoveWarning);
        }

        return $redirectResponse;
    }
}
