<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:24px;">
    <div style="max-width:480px; margin:0 auto; background:#fff; border-radius:12px; padding:32px; text-align:center;">
        <h2 style="color:#111;">Reset Password</h2>
        <p style="color:#555;">Halo {{ $userName }},</p>
        <p style="color:#555;">Gunakan kode OTP berikut untuk reset password akun kamu:</p>
        <div style="font-size:32px; font-weight:bold; letter-spacing:8px; color:#111; background:#f0f0f0; padding:16px; border-radius:8px; margin:24px 0;">
            {{ $otpCode }}
        </div>
        <p style="color:#888; font-size:13px;">Kode berlaku 10 menit. Jangan bagikan kode ini ke siapa pun.</p>
        <p style="color:#888; font-size:13px;">Kalau kamu tidak meminta reset password, abaikan email ini.</p>
    </div>
</body>
</html>