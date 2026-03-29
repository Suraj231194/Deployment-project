<?php

namespace App\Services\Authorization;

use App\Services\Notification\EmailNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SignupEmailOtpService
{
    public function __construct(
        protected EmailNotificationService $emailNotificationService,
    ) {
    }

    // This sends one email OTP for B2C signup after checking resend rules.
    public function sendSignupOtp(string $email): array
    {
        // Step 1: normalize the email once so cache keys and verification lookups stay consistent.
        $normalizedEmail = $this->normalizeEmail($email);

        // Step 2: load one still-active OTP for this email when it already exists.
        $activeSignupOtpData = $this->getActiveSignupOtpData($normalizedEmail);

        // Step 3: stop repeated sends during the resend cooldown window.
        $this->ensureOtpCanBeResent($activeSignupOtpData);

        // Step 4: reuse the active OTP or create a new one.
        $otpCode = $this->getSignupOtpCode($activeSignupOtpData);
        $expiryMinutes = $this->signupOtpExpiryMinutes();
        $expiresAt = now()->addMinutes($expiryMinutes);

        if (is_array($activeSignupOtpData)) {
            $expiresAt = Carbon::parse($activeSignupOtpData['expires_at']);
        }

        // Step 5: store the OTP data in cache for the signup verification step.
        $signupOtpData = [
            'otp_code' => $otpCode,
            'otp_hash' => $this->hashOtp($normalizedEmail, $otpCode),
            'sent_at' => now()->toIso8601String(),
            'expires_at' => $expiresAt->toIso8601String(),
            'failed_attempts' => 0,
        ];

        Cache::put($this->getSignupOtpCacheKey($normalizedEmail), $signupOtpData, $expiresAt);

        // Step 6: clear the earlier verified marker because a new OTP starts one fresh verification cycle.
        Cache::forget($this->getSignupVerifiedEmailCacheKey($normalizedEmail));

        // Step 7: send the OTP email through the shared notification service.
        $this->emailNotificationService->sendSignupEmailOtp($normalizedEmail, $otpCode, $expiryMinutes);

        Log::info('Signup email OTP sent successfully.', [
            'email' => $normalizedEmail,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);

        // Step 8: return the response data needed by the signup screen.
        $responseData = [
            'message' => 'OTP sent to your email successfully.',
            'expires_in_minutes' => $expiryMinutes,
            'resend_available_in_seconds' => $this->signupOtpResendCooldownSeconds(),
        ];

        if (is_array($activeSignupOtpData)) {
            $responseData['message'] = 'OTP sent again. Please use the latest email you received.';
        }

        return $responseData;
    }

    // This verifies the submitted OTP and marks the email as approved for B2C signup.
    public function verifySignupOtp(string $email, string $otp): array
    {
        // Step 1: normalize the input once so OTP lookup and verified cache use one stable key.
        $normalizedEmail = $this->normalizeEmail($email);
        $normalizedOtp = trim($otp);

        // Step 2: load the current OTP session for this email and stop when no active OTP exists.
        $signupOtpData = Cache::get($this->getSignupOtpCacheKey($normalizedEmail));

        if (! is_array($signupOtpData)) {
            throw ValidationException::withMessages([
                'otp' => 'Please request a new OTP first.',
            ]);
        }

        // Step 3: stop the flow cleanly when the OTP has already expired.
        $expiresAt = Carbon::parse($signupOtpData['expires_at'] ?? now()->subSecond());

        if ($expiresAt->isPast()) {
            Cache::forget($this->getSignupOtpCacheKey($normalizedEmail));

            throw ValidationException::withMessages([
                'otp' => 'This OTP has expired. Please request a new OTP.',
            ]);
        }

        // Step 4: block further attempts when the customer has already exhausted the allowed invalid tries.
        $failedAttempts = (int) ($signupOtpData['failed_attempts'] ?? 0);
        $maxOtpAttempts = $this->signupOtpMaxAttempts();

        if ($failedAttempts >= $maxOtpAttempts) {
            Cache::forget($this->getSignupOtpCacheKey($normalizedEmail));

            throw ValidationException::withMessages([
                'otp' => 'Too many invalid OTP attempts. Please request a new OTP.',
            ]);
        }

        // Step 5: compare the submitted OTP with the stored secure hash.
        $expectedOtpHash = (string) ($signupOtpData['otp_hash'] ?? '');
        $submittedOtpHash = $this->hashOtp($normalizedEmail, $normalizedOtp);

        if (! hash_equals($expectedOtpHash, $submittedOtpHash)) {
            $failedAttempts++;

            // Step 6: update the failed-attempt count so repeated invalid OTP guesses are limited.
            $updatedSignupOtpData = [
                'otp_code' => $signupOtpData['otp_code'] ?? null,
                'otp_hash' => $signupOtpData['otp_hash'] ?? null,
                'sent_at' => $signupOtpData['sent_at'] ?? now()->toIso8601String(),
                'expires_at' => $expiresAt->toIso8601String(),
                'failed_attempts' => $failedAttempts,
            ];

            Cache::put($this->getSignupOtpCacheKey($normalizedEmail), $updatedSignupOtpData, $expiresAt);

            Log::warning('Invalid signup email OTP entered.', [
                'email' => $normalizedEmail,
                'failed_attempts' => $failedAttempts,
            ]);

            $message = 'The entered OTP is not valid. Please try again.';

            if ($failedAttempts >= $maxOtpAttempts) {
                $message = 'Too many invalid OTP attempts. Please request a new OTP.';
            }

            throw ValidationException::withMessages([
                'otp' => $message,
            ]);
        }

        // Step 7: store one short-lived verified marker so the final signup submit can trust this email.
        $verifiedEmailData = [
            'email' => $normalizedEmail,
            'verified_at' => now()->toIso8601String(),
        ];

        Cache::put(
            $this->getSignupVerifiedEmailCacheKey($normalizedEmail),
            $verifiedEmailData,
            now()->addMinutes($this->signupVerifiedWindowMinutes())
        );

        // Step 8: remove the used OTP because the email has already been confirmed successfully.
        Cache::forget($this->getSignupOtpCacheKey($normalizedEmail));

        Log::info('Signup email OTP verified successfully.', [
            'email' => $normalizedEmail,
        ]);

        $responseData = [
            'message' => 'Email verified successfully. You can continue with signup now.',
        ];

        return $responseData;
    }

    // This protects final account creation so only OTP-verified B2C emails can register.
    public function ensureSignupEmailIsVerified(string $email): void
    {
        $normalizedEmail = $this->normalizeEmail($email);
        $verifiedEmailData = Cache::get($this->getSignupVerifiedEmailCacheKey($normalizedEmail));

        if (! is_array($verifiedEmailData) || ($verifiedEmailData['email'] ?? null) !== $normalizedEmail) {
            throw ValidationException::withMessages([
                'email' => 'Please verify your email with OTP before continuing.',
            ]);
        }
    }

    // This helps the signup page restore verified-email UI state after other validation errors return the user back to the form.
    public function hasVerifiedSignupEmail(string $email): bool
    {
        $normalizedEmail = $this->normalizeEmail($email);
        $verifiedEmailData = Cache::get($this->getSignupVerifiedEmailCacheKey($normalizedEmail));

        return is_array($verifiedEmailData) && ($verifiedEmailData['email'] ?? null) === $normalizedEmail;
    }

    // This clears the temporary verified marker after a successful final signup create.
    public function clearVerifiedSignupEmail(string $email): void
    {
        $normalizedEmail = $this->normalizeEmail($email);

        Cache::forget($this->getSignupVerifiedEmailCacheKey($normalizedEmail));
    }

    // This ensures resend requests respect the configured cooldown time.
    protected function ensureOtpCanBeResent(?array $signupOtpData = null): void
    {
        if (! is_array($signupOtpData) || empty($signupOtpData['sent_at'])) {
            return;
        }

        $nextAllowedAt = Carbon::parse($signupOtpData['sent_at'])->addSeconds($this->signupOtpResendCooldownSeconds());

        if ($nextAllowedAt->isFuture()) {
            throw ValidationException::withMessages([
                'email' => 'Please wait before requesting another OTP.',
            ]);
        }
    }

    // This returns one active OTP payload only when the current OTP is still valid for the email.
    protected function getActiveSignupOtpData(string $email): ?array
    {
        $signupOtpData = Cache::get($this->getSignupOtpCacheKey($email));

        if (! is_array($signupOtpData)) {
            return null;
        }

        $expiresAt = Carbon::parse($signupOtpData['expires_at'] ?? now()->subSecond());

        if ($expiresAt->isPast()) {
            Cache::forget($this->getSignupOtpCacheKey($email));

            return null;
        }

        return $signupOtpData;
    }

    // This keeps one stable OTP code active during the valid window so repeated resend actions do not confuse the customer.
    protected function getSignupOtpCode(?array $signupOtpData): string
    {
        if (is_array($signupOtpData) && filled($signupOtpData['otp_code'] ?? null)) {
            return (string) $signupOtpData['otp_code'];
        }

        return (string) random_int(100000, 999999);
    }

    // This creates one stable cache key for the active OTP record.
    protected function getSignupOtpCacheKey(string $email): string
    {
        return 'signup_email_otp:'.sha1($email);
    }

    // This creates one stable cache key for the verified-email marker.
    protected function getSignupVerifiedEmailCacheKey(string $email): string
    {
        return 'signup_email_verified:'.sha1($email);
    }

    // This keeps email matching consistent across send, verify, and final signup submit.
    protected function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    // This generates a simple secure OTP hash without storing the plain OTP in cache.
    protected function hashOtp(string $email, string $otp): string
    {
        return hash('sha256', $email.'|'.$otp.'|'.config('app.key'));
    }

    // This returns the OTP expiry window from config.
    protected function signupOtpExpiryMinutes(): int
    {
        return (int) config('common.signup_email_otp.expiry_minutes', 10);
    }

    // This returns the resend cooldown time from config.
    protected function signupOtpResendCooldownSeconds(): int
    {
        return (int) config('common.signup_email_otp.resend_cooldown_seconds', 60);
    }

    // This returns the temporary verified-email lifetime from config.
    protected function signupVerifiedWindowMinutes(): int
    {
        return (int) config('common.signup_email_otp.verified_window_minutes', 30);
    }

    // This returns the maximum number of invalid OTP attempts allowed.
    protected function signupOtpMaxAttempts(): int
    {
        return (int) config('common.signup_email_otp.max_attempts', 5);
    }
}
