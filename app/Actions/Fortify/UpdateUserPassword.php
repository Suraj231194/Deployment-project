<?php

namespace App\Actions\Fortify;

use App\Models\Authorization\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;
    // this method is used for updating the password from profile page.
    public function update(User $user, array $input): void
    {
        // Step 1: validate the current password and the new password fields.
        $validator = Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            // Custom error message for current password validation failure.
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ]);

        //It runs validation ,If validation fails, it throws a ValidationException, Errors are stored in a named error bag: 'updatePassword'
        $validator->validateWithBag('updatePassword');

        // Step 2: prepare the new password data.
        $passwordData = [  'password' => Hash::make($input['password']),];

        // Step 3: update the password timestamp when the column exists in this database.
        if (Schema::hasColumn('users', 'password_updated_at')) {
            $passwordData['password_updated_at'] = now();
        }

        // Step 4: save the new password on the user record.
        $user->forceFill($passwordData);
        $user->save();
    }
}
