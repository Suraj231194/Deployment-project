<?php

namespace App\Actions\Fortify;

use App\Models\Authorization\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Throwable;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;
    // this method is used for forgot password flow.
    public function reset(User $user, array $input): void
    {
        try {
            // Step 1: validate the new password fields from the reset flow.
            Validator::make($input, [ 'password' => $this->passwordRules(), ])->validate();

            // Step 2: save the new hashed password on the user record.
            $attributes = [ 'password' => Hash::make($input['password']), ];

            // Step 3: update the dedicated password-change timestamp only when the column exists in the current database.
            if (Schema::hasColumn('users', 'password_updated_at')) {
                $attributes['password_updated_at'] = now();
            }

            $user->forceFill($attributes)->save();
        } catch (Throwable $exception) {
            Log::error('Failed to reset user password.', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}
