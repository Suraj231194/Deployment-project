<?php

namespace App\Services\Profile;

use App\Models\Authorization\Company;
use App\Models\Authorization\User;
use App\Models\Authorization\UserAddress;
use App\Models\Order\Order;
use App\Models\SupportTicket\SupportTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Throwable;

class ProfileService
{
    // This prepares the current customer's profile data for the existing profile page.
    public function buildMyProfilePageData(User $user): array
    {
        try {
            // Step 1: load the latest user profile with the linked company so the page always shows current business data.
            $profileUser = User::query()
                ->with('company')
                ->findOrFail($user->id);

            // Step 2: load the main saved address used by the current profile page for delivery details.
            $profileAddress = $this->primaryAddressForUser($profileUser);

            // Step 3: prepare the quick summary numbers shown in the profile header cards.
            $profileSummary = $this->buildProfileSummary($profileUser);

            return [
                'portal' => $profileUser->isB2b() ? 'b2b' : 'b2c',
                'profileUser' => $profileUser,
                'profileCompany' => $profileUser->company,
                'profileAddress' => $profileAddress,
                'profileSummary' => $profileSummary,
                'passwordLastChangedLabel' => $this->buildPasswordLastChangedLabel($profileUser),
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build customer profile page data.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This returns the validation rules required for the current customer profile form.
    public function profileValidationRules(User $user): array
    {
        try {
            // Step 1: prepare the common personal fields shared by both B2C and B2B customers.
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
                'phone' => ['nullable', 'string', 'max:30'],
            ];

            // Step 2: add company fields only when the current profile belongs to a B2B customer.
            if ($user->isB2b()) {
                return array_merge($rules, [
                    'designation' => ['nullable', 'string', 'max:255'],
                    'company_name' => ['required', 'string', 'max:255'],
                    'legal_name' => ['nullable', 'string', 'max:255'],
                    'registration_number' => ['nullable', 'string', 'max:255'],
                    'gst_number' => ['nullable', 'string', 'max:50'],
                    'pan_number' => ['nullable', 'string', 'max:50'],
                ]);
            }

            // Step 3: add address fields for B2C customers because the current UI allows editing the main delivery address.
            return array_merge($rules, [
                'address_line1' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:120'],
                'state' => ['nullable', 'string', 'max:120'],
                'postal_code' => ['nullable', 'string', 'max:30'],
                'country' => ['nullable', 'string', 'max:120'],
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to build profile validation rules.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This saves the editable profile fields submitted from the current profile page.
    public function saveMyProfileSection(User $user, array $validated): void
    {
        try {
            DB::transaction(function () use ($user, $validated): void {
                // Step 1: save the user-owned contact fields that belong directly to the account.
                $this->saveUserProfileFields($user, $validated);

                // Step 2: save company information only for B2B customers because those fields belong to the business record.
                if ($user->isB2b()) {
                    $this->saveCompanyProfileFields($user, $validated);
                    return;
                }

                // Step 3: save the primary address for B2C customers because the profile page exposes delivery information.
                $this->savePrimaryAddressFields($user, $validated);
            });

            Log::info('Customer profile updated successfully.', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to save customer profile.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This updates the signed-in customer's password through the shared Fortify password flow.
    public function updateMyPassword(User $user, array $input, UpdatesUserPasswords $passwordUpdater): void
    {
        try {
            // Step 1: reuse the standard Fortify password validation and hashing flow so password rules stay consistent across the application.
            $passwordUpdater->update($user, $input);

            Log::info('Customer password updated successfully.', [
                'user_id' => $user->id,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to update customer password.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This loads the main address row used for the customer profile page.
    protected function primaryAddressForUser(User $user): ?UserAddress
    {
        try {
            return UserAddress::query()
                ->where('user_id', $user->id)
                ->orderByDesc('is_default_shipping')
                ->orderByDesc('is_default_billing')
                ->orderBy('id')
                ->first();
        } catch (Throwable $exception) {
            Log::error('Failed to load primary customer address.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This prepares the quick profile summary cards.
    protected function buildProfileSummary(User $user): array
    {
        try {
            return [
                'orders_count' => Order::query()->where('placed_by_user_id', $user->id)->count(),
                'tickets_count' => SupportTicket::query()->where('owner_user_id', $user->id)->count(),
                'status_label' => $this->humanProfileStatus($user),
            ];
        } catch (Throwable $exception) {
            Log::error('Failed to build customer profile summary.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This prepares a business-friendly label for the latest known password change time.
    protected function buildPasswordLastChangedLabel(User $user): string
    {
        try {
            // Step 1: prefer the dedicated password timestamp and fall back to account creation for older databases.
            $passwordUpdatedAt = $user->password_updated_at ?: $user->created_at;

            if (! $passwordUpdatedAt) {
                return 'Last changed date not available';
            }

            // Step 2: count full day difference so the UI can show a simple and stable business label.
            $daysSincePasswordChange = $passwordUpdatedAt->copy()->startOfDay()->diffInDays(now()->startOfDay());

            if ($daysSincePasswordChange === 0) {
                return 'Last changed today';
            }

            if ($daysSincePasswordChange === 1) {
                return 'Last changed 1 day ago';
            }

            return 'Last changed '.$daysSincePasswordChange.' days ago';
        } catch (Throwable $exception) {
            Log::error('Failed to build password last changed label.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            return 'Last changed date not available';
        }
    }

    // This saves the direct user account fields shown on the profile page.
    protected function saveUserProfileFields(User $user, array $validated): void
    {
        try {
            $user->fill([
                'name' => trim((string) $validated['name']),
                'email' => trim((string) $validated['email']),
                'phone' => isset($validated['phone']) ? trim((string) $validated['phone']) : null,
                'designation' => isset($validated['designation']) ? trim((string) $validated['designation']) : $user->designation,
            ]);

            $user->save();
        } catch (Throwable $exception) {
            Log::error('Failed to save direct user profile fields.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This saves the linked company data for a B2B profile.
    protected function saveCompanyProfileFields(User $user, array $validated): void
    {
        try {
            // Step 1: load the linked company or create one when an older B2B account does not yet have it.
            $company = $user->company ?: Company::query()->create([
                'name' => trim((string) $validated['company_name']),
                'is_active' => true,
            ]);

            // Step 2: save the submitted business master fields on the company record.
            $company->fill([
                'name' => trim((string) $validated['company_name']),
                'legal_name' => isset($validated['legal_name']) ? trim((string) $validated['legal_name']) : null,
                'registration_number' => isset($validated['registration_number']) ? trim((string) $validated['registration_number']) : null,
                'gst_number' => isset($validated['gst_number']) ? trim((string) $validated['gst_number']) : null,
                'pan_number' => isset($validated['pan_number']) ? trim((string) $validated['pan_number']) : null,
            ]);
            $company->save();

            // Step 3: make sure the current B2B user stays linked to the saved company record.
            if ((int) $user->company_id !== (int) $company->id) {
                $user->company_id = (int) $company->id;
                $user->save();
            }
        } catch (Throwable $exception) {
            Log::error('Failed to save company profile fields.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This saves the primary B2C address shown on the profile page.
    protected function savePrimaryAddressFields(User $user, array $validated): void
    {
        try {
            $address = $this->primaryAddressForUser($user);

            // Step 1: create a primary address row only when the user is saving address data for the first time.
            if (! $address) {
                $address = new UserAddress([
                    'user_id' => $user->id,
                    'is_default_shipping' => true,
                    'is_default_billing' => true,
                ]);
            }

            // Step 2: save the submitted address fields onto the primary address row.
            $address->fill([
                'line1' => isset($validated['address_line1']) ? trim((string) $validated['address_line1']) : null,
                'city' => isset($validated['city']) ? trim((string) $validated['city']) : null,
                'state' => isset($validated['state']) ? trim((string) $validated['state']) : null,
                'postal_code' => isset($validated['postal_code']) ? trim((string) $validated['postal_code']) : null,
                'country' => isset($validated['country']) ? trim((string) $validated['country']) : null,
            ]);
            $address->save();
        } catch (Throwable $exception) {
            Log::error('Failed to save primary customer address.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }


    // This converts the account status into a simple label used by the profile summary UI.
    protected function humanProfileStatus(User $user): string
    {
        return match ($user->status) {
            'active' => 'Active',
            'pending_approval' => 'Pending Approval',
            'blocked' => 'Blocked',
            default => 'Unknown',
        };
    }
}
