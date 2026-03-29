<?php

namespace App\Actions\Fortify;

use App\Models\Authorization\Company;
use App\Models\Authorization\User;
use App\Services\Authorization\RolePermissionService;
use App\Services\Authorization\SignupEmailOtpService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Throwable;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function __construct( protected RolePermissionService $rolePermissionService,  protected SignupEmailOtpService $signupOtpService, ) {
    }

    // This validates registration input and creates a user through the Fortify signup flow.
    public function create(array $input): User
    {
        // Step 1: prepare one clean signup data array used across the full flow.
        $signupData = $this->prepareSignupData($input);

        // Step 2: validate the signup data before any database work starts.
        $this->validateSignupData($signupData);

        try {
            // Step 3: create the correct account type based on the signup flow.
            if ($this->isB2bSignup($signupData)) {
                $createdUser = $this->createB2BUser($signupData);
            } else {
                $createdUser = $this->createB2CUser($signupData);
            }

            // Step 4: write one success log for the completed signup.
            Log::info('New user account created successfully.', [ 'user_id' => $createdUser->id,  'email' => $createdUser->email,   'user_type' => $createdUser->user_type, ]);
        } catch (Throwable $exception) {
            Log::error('Failed to create new user.', [ 'email' => $signupData['email'] ?? null,  'error' => $exception->getMessage(), ]);

            throw ValidationException::withMessages([   'email' => 'Unable to create the account right now. Please try again.',  ]);
        }

        return $createdUser;
    }

    private function prepareSignupData(array $input): array
    {
        // Step 1: resolve the final user type for the signup flow.
        $input['user_type'] = $input['user_type'] ?? (($input['accountType'] ?? null) === 'business' ? 'b2b' : 'b2c');

        // Step 2: build one full name for B2C signup when the form uses first and last name fields.
        $input['name'] = $input['name']
            ?? trim(((string) ($input['first_name'] ?? '')).' '.((string) ($input['last_name'] ?? '')));

        return $input;
    }

    private function validateSignupData(array $signupData): void
    {
        // Step 1: load the allowed B2B designation values from config.
        $b2bDesignationValues = array_keys(config('common.b2b_designation_options', []));

        // Step 2: validate the signup data for both B2B and B2C flows.
        $validator = Validator::make($signupData, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'user_type' => ['required', Rule::in(['b2c', 'b2b'])],
            'b2b_type' => ['nullable', 'required_if:user_type,b2b', Rule::in($b2bDesignationValues)],
            'company_name' => ['nullable', 'required_if:user_type,b2b', 'string', 'max:255'],
            'company_type' => ['nullable', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'gst_number' => ['nullable', 'required_if:user_type,b2b', 'string', 'max:20'],
            'pan_number' => ['nullable', 'string', 'max:20'],
            'reg_number' => ['nullable', 'string', 'max:255'],
            'established_year' => ['nullable', 'integer', 'min:1900', 'max:'.date('Y')],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'required_if:user_type,b2b', 'string', 'max:20'],
            'alt_phone' => ['nullable', 'string', 'max:20'],
            'address_1' => ['nullable', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:128'],
            'state' => ['nullable', 'string', 'max:128'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:128'],
            'password' => $this->passwordRules(),
        ]);

        $validator->validate();
    }

    private function createB2BUser(array $signupData): User
    {
        // Step 1: create the B2B account records inside one transaction.
        $createdUser = DB::transaction(function () use ($signupData): User {
            // Step 1.1: save the company linked to the B2B signup.
            $company = $this->saveCompany($signupData);

            // Step 1.2: keep the B2B account in pending approval state.
            $accountStatus = 'pending_approval';
            $approvedAt = null;

            // Step 1.3: create the B2B user account.
            $userData = $this->getUserData($signupData, $company, $accountStatus, $approvedAt);
            $createdUser = User::query()->create($userData);

            // Step 1.4: save the default address when address details are present.
            $this->saveDefaultAddress($createdUser, $signupData);

            return $createdUser;
        });

        // Step 2: assign the default B2B role after the account is created.
        $this->rolePermissionService->assignDefaultRole($createdUser);

        return $createdUser;
    }

    private function createB2CUser(array $signupData): User
    {
        // Step 1: make sure the B2C email is verified before account creation.
        $signupEmail = (string) ($signupData['email'] ?? '');
        $this->signupOtpService->ensureSignupEmailIsVerified($signupEmail);

        // Step 2: create the B2C account records inside one transaction.
        $createdUser = DB::transaction(function () use ($signupData): User {
            // Step 2.1: keep the B2C account active after signup.
            $accountStatus = 'active';
            $approvedAt = now();

            // Step 2.2: create the B2C user account.
            $userData = $this->getUserData($signupData, null, $accountStatus, $approvedAt);
            $createdUser = User::query()->create($userData);

            // Step 2.3: save the default address when address details are present.
            $this->saveDefaultAddress($createdUser, $signupData);

            return $createdUser;
        });

        // Step 3: assign the default B2C role after the account is created.
        $this->rolePermissionService->assignDefaultRole($createdUser);

        // Step 4: clear the one-time email verification after successful signup.
        $this->signupOtpService->clearVerifiedSignupEmail($signupEmail);

        return $createdUser;
    }

    private function isB2bSignup(array $input): bool
    {
        return ($input['user_type'] ?? null) === 'b2b';
    }

    private function saveCompany(array $signupData): Company
    {
        // Step 1: prepare the company data that should be saved for B2B signup.
        $companyData = [
            'name' => $signupData['company_name'],
            'legal_name' => $signupData['legal_name'] ?? null,
            'pan_number' => $signupData['pan_number'] ?? null,
            'registration_number' => $signupData['reg_number'] ?? null,
            'established_year' => isset($signupData['established_year']) ? (int) $signupData['established_year'] : null,
            'website' => $signupData['website'] ?? null,
            'company_type' => $signupData['company_type'] ?? null,
            'is_active' => true,
        ];

        // Step 2: create or update the company by GST number.
        return Company::query()->updateOrCreate(
            ['gst_number' => $signupData['gst_number']],
            $companyData,
        );
    }

    private function getUserData(array $signupData, ?Company $company, string $accountStatus, $approvedAt): array
    {
        // Step 1: prepare the user account data.
        $userData = [
            'name' => $signupData['name'],
            'email' => $signupData['email'],
            'user_type' => $signupData['user_type'],
            'b2b_type' => $this->isB2bSignup($signupData) ? ($signupData['b2b_type'] ?? null) : null,
            'phone' => $signupData['phone'] ?? null,
            'alt_phone' => $signupData['alt_phone'] ?? null,
            'company_id' => $company?->id,
            'status' => $accountStatus,
            'approved_at' => $approvedAt,
            'approved_by_user_id' => null,
            'created_by_user_id' => null,
            'password' => Hash::make($signupData['password']),
            'password_updated_at' => now(),
        ];

        return $userData;
    }

    private function saveDefaultAddress(User $user, array $input): void
    {
        // Step 1: stop when the signup data does not contain a full address.
        if (! $this->hasAddressData($input)) {
            return;
        }

        // Step 2: prepare the default address data.
        $addressData = [
            'line1' => $input['address_1'],
            'line2' => $input['address_2'] ?? null,
            'city' => $input['city'],
            'state' => $input['state'],
            'postal_code' => $input['pincode'],
            'country' => $input['country'] ?? 'India',
            'is_default_shipping' => true,
            'is_default_billing' => true,
        ];

        // Step 3: save the default address for the new user.
        $user->addresses()->create($addressData);
    }

    private function hasAddressData(array $input): bool
    {
        return Schema::hasTable('user_address')
            && filled($input['address_1'] ?? null)
            && filled($input['city'] ?? null)
            && filled($input['state'] ?? null)
            && filled($input['pincode'] ?? null);
    }
}
