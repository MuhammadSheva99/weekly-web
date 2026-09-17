<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:24px;">
    <div style="max-width:480px; margin:0 auto; background:#fff; border-radius:12px; padding:32px;">
        <h2 style="color:#111; margin-top:0;">{{ $judul }}</h2>
        <p style="color:#555; line-height:1.6;">{{ $pesan }}</p>

        @if ($ctaLabel && $ctaUrl)
            <a href="{{ $ctaUrl }}" style="display:inline-block; margin-top:16px; padding:12px 24px; background:#111; color:#fff; text-decoration:none; border-radius:8px;">
                {{ $ctaLabel }}
            </a>
        @endif

        <p style="color:#aaa; font-size:12px; margin-top:32px;">Email otomatis dari Weekly Performance Management - PT Indoboga Makmur Pratama.</p>
    </div>
</body>
</html>