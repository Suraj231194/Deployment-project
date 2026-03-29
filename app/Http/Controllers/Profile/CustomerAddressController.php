<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\Profile\CustomerAddressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CustomerAddressController extends Controller
{
    // This renders the signed-in customer's live address book page.
    public function index(Request $request, CustomerAddressService $customerAddressService): View
    {
        try {
            // Step 1: prepare the saved address list and page context from one service so the controller stays simple.
            $pageData = $customerAddressService->buildAddressPageData($request->user());

            // Step 2: render the existing addresses page with backend data.
            return view('userProfile.addresses.index', $pageData);
        } catch (Throwable $exception) {
            Log::error('Failed to load customer addresses page.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            return $this->viewWithError('userProfile.addresses.index', [
                'portal' => $request->user()?->isB2b() ? 'b2b' : 'b2c',
                'savedAddresses' => collect(),
            ], $exception, 'Unable to load addresses right now.');
        }
    }

    // This stores one brand-new saved address for the signed-in customer.
    public function store(Request $request, CustomerAddressService $customerAddressService): RedirectResponse
    {
        try {
            $user = $request->user();

            // Step 1: validate the add-address form through one named error bag so the add modal can show clear field messages.
            $validated = Validator::make(
                $request->all(),
                $customerAddressService->addressValidationRules()
            )->validateWithBag('addressCreate');

            // Step 2: save the new address in the shared address service.
            $customerAddressService->createAddressForUser($user, $validated);

            return redirect()
                ->route('customer.addresses.preview')
                ->with('status', 'Address added successfully.');
        } catch (ValidationException $exception) {
            Log::warning('Customer address create validation failed.', [
                'user_id' => $request->user()?->id,
                'errors' => $exception->errors(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors($exception->validator->errors(), $exception->errorBag ?: 'addressCreate')
                ->with('open_modal', 'addAddressModal');
        } catch (Throwable $exception) {
            Log::error('Failed to store customer address.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unable to save address right now.')
                ->with('open_modal', 'addAddressModal');
        }
    }

    // This updates one saved address directly from the address list page.
    public function update(
        Request $request,
        int $addressId,
        CustomerAddressService $customerAddressService
    ): RedirectResponse {
        try {
            $user = $request->user();
            $errorBagName = 'addressUpdate_'.$addressId;

            // Step 1: validate the submitted row using an address-specific error bag so only that row shows field errors.
            $validated = Validator::make(
                $request->all(),
                $customerAddressService->addressValidationRules()
            )->validateWithBag($errorBagName);

            // Step 2: save the updated address through one shared business service.
            $customerAddressService->updateAddressForUser($user, $addressId, $validated);

            return redirect()
                ->route('customer.addresses.preview')
                ->with('status', 'Address updated successfully.');
        } catch (ValidationException $exception) {
            Log::warning('Customer address update validation failed.', [
                'user_id' => $request->user()?->id,
                'user_address_id' => $addressId,
                'errors' => $exception->errors(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors($exception->validator->errors(), $exception->errorBag ?: 'addressUpdate_'.$addressId)
                ->with('editing_address_id', $addressId);
        } catch (Throwable $exception) {
            Log::error('Failed to update customer address from address page.', [
                'user_id' => $request->user()?->id,
                'user_address_id' => $addressId,
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unable to update address right now.')
                ->with('editing_address_id', $addressId);
        }
    }
}
