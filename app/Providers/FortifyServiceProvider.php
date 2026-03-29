<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Models\Authorization\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Step 1: register the auth page views used by Fortify.
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.signup'));
        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn (Request $request) => view('auth.reset-password', ['request' => $request]));
        Fortify::confirmPasswordView(fn () => view('auth.confirm-password'));

        // Step 2: validate the login user and block inactive account states.
        Fortify::authenticateUsing(function (Request $request): ?User {
            $loginEmail = (string) $request->input('email');
            $loginPassword = (string) $request->input('password');
            $user = User::query()->where('email', $loginEmail)->first();

            if (! $user) {
                return null;
            }

            if (! Hash::check($loginPassword, (string) $user->password)) {
                return null;
            }

            if ($user->status === 'pending_approval') {
                throw ValidationException::withMessages([
                    'email' => 'Your B2B account is pending admin approval.',
                ]);
            }

            if ($user->status === 'blocked') {
                throw ValidationException::withMessages([
                    'email' => 'Your account is blocked. Please contact support.',
                ]);
            }

            if ($user->status !== 'active') {
                throw ValidationException::withMessages([
                    'email' => 'Your account is not active.',
                ]);
            }

            return $user;
        });

        // Step 3: register the Fortify action classes used by signup and password flows.
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Step 4: throttle repeated login attempts.
        RateLimiter::for('login', function (Request $request) {
            $loginFieldValue = (string) $request->input(Fortify::username());
            $throttleKeyText = Str::lower($loginFieldValue).'|'.$request->ip();
            $throttleKey = Str::transliterate($throttleKeyText);

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
