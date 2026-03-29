<?php

namespace App\Http\Controllers\Authorization;

use App\Http\Controllers\Controller;
use App\Models\Authorization\User;
use App\Services\Authorization\SignupEmailOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class SignupEmailOtpController extends Controller
{
    // This sends the signup OTP to the entered B2C email address.
    public function sendOtp(Request $request, SignupEmailOtpService $signupOtpService): JsonResponse
    {
        try {
            // Step 1: validate the email before the OTP send flow starts.
            $validatedData = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            ]);

            // Step 2: send the OTP through the shared signup OTP service.
            $sendResult = $signupOtpService->sendSignupOtp($validatedData['email']);

            // Step 3: return one simple success response for the signup page AJAX flow.
            return response()->json([
                'status' => 'success',
                'message' => $sendResult['message'],
                'expires_in_minutes' => $sendResult['expires_in_minutes'],
                'resend_available_in_seconds' => $sendResult['resend_available_in_seconds'],
            ]);
        } catch (ValidationException $exception) {
            Log::warning('Signup email OTP send validation failed.', [
                'email' => $request->input('email'),
                'errors' => $exception->errors(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => collect($exception->errors())->flatten()->first() ?: 'Please review the email details and try again.',
                'errors' => $exception->errors(),
            ], 422);
        } catch (Throwable $exception) {
            Log::error('Signup email OTP send request failed.', [
                'email' => $request->input('email'),
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to send OTP right now. Please try again.',
            ], 500);
        }
    }

    // This verifies the submitted email OTP for the B2C signup flow.
    public function verifyOtp(Request $request, SignupEmailOtpService $signupOtpService): JsonResponse
    {
        try {
            // Step 1: validate the email and OTP format before verification starts.
            $validatedData = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255'],
                'otp' => ['required', 'digits:6'],
            ]);

            // Step 2: verify the OTP through the shared signup OTP service.
            $verifyResult = $signupOtpService->verifySignupOtp(
                $validatedData['email'],
                $validatedData['otp'],
            );

            // Step 3: return one simple success response for the signup page AJAX flow.
            return response()->json([
                'status' => 'success',
                'message' => $verifyResult['message'],
            ]);
        } catch (ValidationException $exception) {
            Log::warning('Signup email OTP verification failed.', [
                'email' => $request->input('email'),
                'errors' => $exception->errors(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => collect($exception->errors())->flatten()->first() ?: 'Please review the OTP details and try again.',
                'errors' => $exception->errors(),
            ], 422);
        } catch (Throwable $exception) {
            Log::error('Signup email OTP verify request failed.', [
                'email' => $request->input('email'),
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to verify OTP right now. Please try again.',
            ], 500);
        }
    }
}
