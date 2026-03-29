@php
    $appName = config('app.name', 'Biogenix');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your signup email OTP</title>
</head>
<body style="margin:0; padding:24px; background:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; margin:0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
        <tr>
            <td style="padding:32px;">
                <p style="margin:0; font-size:12px; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:#1A4D2E;">Email Verification</p>
                <h1 style="margin:16px 0 0; font-size:28px; line-height:1.25; color:#0f172a;">Confirm your email address</h1>
                <p style="margin:16px 0 0; font-size:15px; line-height:1.8; color:#475569;">
                    We received a request to verify this email for a new {{ $appName }} customer account.
                </p>

                <div style="margin-top:24px; padding:18px; border-radius:14px; background:#eff6ff; text-align:center;">
                    <p style="margin:0; font-size:12px; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:#1d4ed8;">Your OTP</p>
                    <p style="margin:10px 0 0; font-size:32px; font-weight:800; letter-spacing:0.28em; color:#0f172a;">{{ $otpCode }}</p>
                </div>

                <p style="margin:24px 0 0; font-size:14px; line-height:1.8; color:#475569;">
                    This OTP will expire in {{ $expiryMinutes }} minutes.
                </p>

                <p style="margin:12px 0 0; font-size:14px; line-height:1.8; color:#475569;">
                    If you did not request this verification, you can safely ignore this email.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
