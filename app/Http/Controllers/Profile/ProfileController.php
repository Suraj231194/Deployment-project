<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\Profile\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Throwable;

class ProfileController extends Controller
{
    // This renders the live profile page for the signed-in customer.
    public function showMyProfilePage(Request $request, ProfileService $profileService): View
    {
        try {
            // Step 1: ask the profile service for the current customer profile data used by the existing page UI.
            $pageData = $profileService->buildMyProfilePageData($request->user());

            // Step 2: render the existing profile page with real backend data.
            return view('userProfile.profile.index', $pageData);
        } catch (Throwable $exception) {
            Log::error('Failed to load customer profile page.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            return $this->viewWithError('userProfile.profile.index', [
                'portal' => 'b2c',
                'profileUser' => $request->user(),
                'profileCompany' => null,
                'profileAddress' => null,
                'profileSummary' => [
                    'orders_count' => 0,
                    'tickets_count' => 0,
                    'status_label' => 'Unknown',
                ],
            ], $exception, 'Unable to load profile right now.');
        }
    }

    // This saves the editable profile details shown on the current profile page.
    public function updateMyProfileSection(Request $request, ProfileService $profileService): RedirectResponse
    {
        try {
            $user = $request->user();

            // Step 1: validate the submitted profile fields using rules prepared for the current customer type.
            $validated = $request->validate($profileService->profileValidationRules($user));

            // Step 2: save the approved profile changes through one shared business service.
            $profileService->saveMyProfileSection($user, $validated);

            return redirect()
                ->route('customer.profile.preview')
                ->with('status', 'Profile updated successfully.');
        } catch (Throwable $exception) {
            Log::error('Failed to update customer profile.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            return $this->redirectBackWithError($exception, 'Unable to update profile.');
        }
    }

    // This updates only the signed-in customer's password from the profile security modal.
    public function updateMyPassword(
        Request $request,
        ProfileService $profileService,
        UpdatesUserPasswords $passwordUpdater
    ): RedirectResponse {
        try {
            $user = $request->user();

            // Step 1: keep the password form input focused on password fields only so it does not trigger profile field validation.
            $input = $request->only(['current_password', 'password', 'password_confirmation']);

            // Step 2: reuse the shared password update flow so the same business rules apply everywhere.
            $profileService->updateMyPassword($user, $input, $passwordUpdater);

            return redirect()
                ->route('customer.profile.preview')
                ->with('status', 'Password updated successfully.');
        } catch (ValidationException $exception) {
            Log::warning('Customer password update validation failed.', [
                'user_id' => $request->user()?->id,
                'errors' => $exception->errors(),
            ]);

            return redirect()
                ->back()
                ->withErrors($exception->validator->errors(), $exception->errorBag ?: 'updatePassword')
                ->with('open_modal', 'changePasswordModal');
        } catch (Throwable $exception) {
            Log::error('Failed to update customer password from profile page.', [
                'user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Unable to update password.')
                ->with('open_modal', 'changePasswordModal');
        }
    }

}
