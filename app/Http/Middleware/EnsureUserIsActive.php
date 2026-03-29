<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // if user is not guest and status is not active, log out the user and redirect to login page with appropriate message.
        if ($user && $user->status !== 'active') {
            $message = $user->status === 'pending_approval'
                ? 'Your account is pending admin approval.'
                : 'Your account is blocked. Please contact support.';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
