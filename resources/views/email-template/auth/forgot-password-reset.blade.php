@php
    $appName = config('app.name', 'Biogenix');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset your password</title>
</head>
<body style="margin:0; padding:24px; background:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; margin:0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
        <tr>
            <td style="padding:32px;">
                <p style="margin:0; font-size:12px; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:#1A4D2E;">Account Recovery</p>
                <h1 style="margin:16px 0 0; font-size:28px; line-height:1.25; color:#0f172a;">Reset your password</h1>
                <p style="margin:16px 0 0; font-size:15px; line-height:1.8; color:#475569;">
                    Hello {{ $user->name ?: 'Biogenix User' }},
                </p>
                <p style="margin:12px 0 0; font-size:15px; line-height:1.8; color:#475569;">
                    We received a request to reset the password for your {{ $appName }} account. Use the button below to create a new password.
                </p>

                <p style="margin:28px 0 0;">
                    <a href="{{ $resetUrl }}" style="display:inline-block; padding:14px 24px; border-radius:12px; background:#1A4D2E; color:#ffffff; text-decoration:none; font-size:15px; font-weight:700;">
                        Reset Password
                    </a>
                </p>

                <p style="margin:24px 0 0; font-size:14px; line-height:1.8; color:#475569;">
                    This reset link will expire in {{ $expiryMinutes }} minutes.
                </p>

                <p style="margin:12px 0 0; font-size:14px; line-height:1.8; color:#475569;">
                    If you did not request a password reset, you can safely ignore this email.
                </p>

                <div style="margin-top:28px; padding:16px; border-radius:12px; background:#eff6ff; color:#1e3a8a; font-size:13px; line-height:1.8;">
                    Security note: {{ $appName }} will never ask for your password over email or phone.
                </div>

                <p style="margin:28px 0 0; font-size:13px; line-height:1.8; color:#64748b;">
                    If the button does not open, copy and paste this link into your browser:<br>
                    <span style="word-break:break-all; color:#1A4D2E;">{{ $resetUrl }}</span>
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
