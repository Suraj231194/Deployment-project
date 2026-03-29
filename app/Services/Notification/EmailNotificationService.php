<?php

namespace App\Services\Notification;

use App\Models\Authorization\User;
use App\Models\Order\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class EmailNotificationService
{
    // This sends the signup email OTP so the customer can verify email before account creation.
    public function sendSignupEmailOtp(string $email, string $otpCode, int $expiryMinutes): void
    {
        try {
            // Step 1: prepare the OTP email payload in one business-friendly structure.
            $emailPayload = $this->buildSignupEmailOtpPayload($email, $otpCode, $expiryMinutes);

            // Step 2: send the prepared OTP email through the configured provider.
            $this->sendEmail($emailPayload);

            Log::info('Signup OTP email sent successfully.', [
                'email' => $email,
                'provider' => $this->configuredProvider(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to send signup OTP email.', [
                'email' => $email,
                'provider' => $this->configuredProvider(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This sends the forgot-password email through the configured provider using one shared business flow.
    public function sendForgotPasswordResetLink(User $user, string $resetUrl): void
    {
        try {
            // Step 1: prepare the business email payload in one simple structure so provider-specific methods stay easy to replace later.
            $emailPayload = $this->buildForgotPasswordEmailPayload($user, $resetUrl);

            // Step 2: send the prepared email through the configured provider.
            $this->sendEmail($emailPayload);

            Log::info('Forgot password email sent successfully.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'provider' => $this->configuredProvider(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to send forgot password email.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'provider' => $this->configuredProvider(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This sends the order-submitted confirmation email after the order has been created successfully.
    public function sendOrderSubmittedConfirmation(User $user, Order $order): void
    {
        try {
            // Step 1: load the order summary details needed by the confirmation email in one predictable structure.
            $preparedOrder = $this->prepareOrderForSubmittedEmail($order);

            // Step 2: prepare the business email payload using the submitted order details.
            $emailPayload = $this->buildOrderSubmittedEmailPayload($user, $preparedOrder);

            // Step 3: send the prepared email through the shared provider flow.
            $this->sendEmail($emailPayload);

            Log::info('Order submitted email sent successfully.', [
                'user_id' => $user->id,
                'order_id' => $preparedOrder->id,
                'items_count' => $preparedOrder->items->count(),
                'email' => $user->email,
                'provider' => $this->configuredProvider(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to send order submitted email.', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'email' => $user->email,
                'provider' => $this->configuredProvider(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This routes the prepared email payload to the currently configured provider.
    public function sendEmail(array $emailPayload): void
    {
        try {
            $provider = $this->configuredProvider();

            // Step 1: keep provider switching centralized so future provider changes need only one small update.
            if ($provider === 'brevo') {
                $this->sendWithBrevo($emailPayload);

                return;
            }

            // we can configure here another provider like SendGrid or Mailgun in the future and route here based on the same provider config value without changing the calling code
            if ($provider === 'log') {
                $this->sendToLogChannel($emailPayload);

                return;
            }

            throw new RuntimeException("Unsupported email provider [{$provider}] configured for notifications.");
        } catch (Throwable $exception) {
            Log::error('Failed to route email through the configured provider.', [
                'provider' => $this->configuredProvider(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    // This returns the configured provider name in one place for easier maintenance.
    protected function configuredProvider(): string
    {
        return strtolower(trim((string) config('common.email_notifications.provider', 'log')));
    }

    // This builds the forgot-password email content used by any provider.
    protected function buildForgotPasswordEmailPayload(User $user, string $resetUrl): array
    {
        $expiryMinutes = (int) config('auth.passwords.'.config('fortify.passwords').'.expire', 60);

        // Step 1: render the branded email body through a normal Blade view so the business copy stays easy to edit later.
        $htmlContent = view('email-template.auth.forgot-password-reset', [
            'user' => $user,
            'resetUrl' => $resetUrl,
            'expiryMinutes' => $expiryMinutes,
        ])->render();

        // Step 2: prepare one provider-neutral payload used by the shared send flow.
        return [
            'to_email' => $user->email,
            'to_name' => $user->name,
            'subject' => 'Reset your Biogenix account password',
            'html_content' => $htmlContent,
            'text_content' => trim(strip_tags($htmlContent)),
        ];
    }

    // This builds the signup OTP email content used by any provider.
    protected function buildSignupEmailOtpPayload(string $email, string $otpCode, int $expiryMinutes): array
    {
        // Step 1: render the branded OTP email body so business copy stays easy to maintain.
        $htmlContent = view('email-template.auth.signup-email-otp', [
            'otpCode' => $otpCode,
            'expiryMinutes' => $expiryMinutes,
        ])->render();

        // Step 2: prepare one provider-neutral payload used by the shared send flow.
        return [
            'to_email' => $email,
            'to_name' => 'Biogenix Customer',
            'subject' => 'Verify your email for Biogenix signup',
            'html_content' => $htmlContent,
            'text_content' => trim(strip_tags($htmlContent)),
        ];
    }

    // This builds the order-submitted email content used by any provider.
    protected function buildOrderSubmittedEmailPayload(User $user, Order $order): array
    {
        // Step 1: render the order confirmation body through a normal Blade view so operations can update the message later without touching provider code.
        $htmlContent = view('email-template.order.order-submitted', [
            'user' => $user,
            'order' => $order,
        ])->render();

        // Step 2: prepare one provider-neutral payload used by the shared send flow.
        return [
            'to_email' => $user->email,
            'to_name' => $user->name,
            'subject' => 'Your Biogenix order #'.$order->id.' has been submitted',
            'html_content' => $htmlContent,
            'text_content' => trim(strip_tags($htmlContent)),
        ];
    }

    // This ensures the order confirmation email always receives the latest item rows and totals in one consistent structure.
    protected function prepareOrderForSubmittedEmail(Order $order): Order
    {
        $preparedOrder = $order->loadMissing([
            'items' => fn ($builder) => $builder->orderBy('sort_order')->orderBy('id'),
        ]);

        Log::info('Prepared order details for submitted email.', [
            'order_id' => $preparedOrder->id,
            'items_count' => $preparedOrder->items->count(),
            'total_amount' => (float) $preparedOrder->total_amount,
        ]);

        return $preparedOrder;
    }

    // This sends the email through Brevo's SMTP email API.
    protected function sendWithBrevo(array $emailPayload): void
    {
        $apiKey = trim((string) config('common.email_notifications.brevo.api_key', ''));
        $baseUrl = rtrim((string) config('common.email_notifications.brevo.base_url', 'https://api.brevo.com/v3'), '/');
        $timeoutSeconds = (int) config('common.email_notifications.brevo.timeout_seconds', 15);

        // Step 1: stop the send flow early when Brevo is selected but credentials are missing.
        if ($apiKey === '') {
            throw new RuntimeException('Brevo API key is not configured for email notifications.');
        }

        // Step 2: build the provider request payload using the shared business email structure.
        $requestPayload = [
            'sender' => [
                'name' => (string) config('common.email_notifications.from_name', config('app.name')),
                'email' => (string) config('common.email_notifications.from_email', 'noreply@example.com'),
            ],
            'to' => [[
                'email' => $emailPayload['to_email'],
                'name' => $emailPayload['to_name'],
            ]],
            'subject' => $emailPayload['subject'],
            'htmlContent' => $emailPayload['html_content'],
            'textContent' => $emailPayload['text_content'],
        ];

        // Step 3: prepare one HTTP client and apply SSL settings in one place so local and production environments stay easy to manage.
        $httpClient = Http::timeout($timeoutSeconds)
            ->withHeaders([
                'accept' => 'application/json',
                'api-key' => $apiKey,
                'content-type' => 'application/json',
            ]);

        // Step 4: apply SSL verification settings before calling Brevo so local certificate issues can be handled through configuration.
        $httpClient = $this->applyBrevoSslConfiguration($httpClient);

        // Step 5: call Brevo and fail clearly when the provider rejects the request.
        $response = $httpClient->post($baseUrl.'/smtp/email', $requestPayload);

        if (! $response->successful()) {
            Log::error('Brevo rejected the email notification request.', [
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'recipient' => $emailPayload['to_email'],
            ]);

            throw new RuntimeException('Brevo could not accept the forgot password email request.');
        }
    }

    // This applies SSL verification settings so environments can use a trusted CA bundle without changing the send logic.
    protected function applyBrevoSslConfiguration(PendingRequest $httpClient): PendingRequest
    {
        $verifySsl = filter_var(config('common.email_notifications.brevo.verify_ssl', true), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        $caBundlePath = trim((string) config('common.email_notifications.brevo.ca_bundle_path', ''));

        // Step 1: use a configured CA bundle when the local PHP installation does not already trust the required certificate chain.
        if ($caBundlePath !== '') {
            if (! is_file($caBundlePath)) {
                throw new RuntimeException("Brevo CA bundle file was not found at [{$caBundlePath}].");
            }

            Log::info('Brevo email client is using a configured CA bundle.', [
                'ca_bundle_path' => $caBundlePath,
            ]);

            return $httpClient->withOptions([
                'verify' => $caBundlePath,
            ]);
        }

        // Step 2: allow local teams to disable SSL verification temporarily while certificate trust is being fixed on the machine.
        if ($verifySsl === false) {
            Log::warning('Brevo email client SSL verification is disabled by configuration. This should be used only for local troubleshooting.', [
                'provider' => 'brevo',
            ]);

            return $httpClient->withOptions([
                'verify' => false,
            ]);
        }

        // Step 3: keep the provider on normal certificate verification when no local override is required.
        return $httpClient;
    }

    // This logs the prepared email payload for local or non-delivery environments.
    protected function sendToLogChannel(array $emailPayload): void
    {
        Log::info('Email notification logged instead of sent.', [
            'provider' => 'log',
            'recipient' => $emailPayload['to_email'],
            'subject' => $emailPayload['subject'],
        ]);
    }
}
