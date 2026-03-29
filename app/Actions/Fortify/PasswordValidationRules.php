<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    protected function passwordRules(): array
    {
        // This keeps the same password validation rule across signup, reset, and password update.
        $passwordRules = ['required', 'string', Password::default(), 'confirmed'];

        return $passwordRules;
    }
}
