<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 8px; padding: 40px; }
        .logo { text-align: center; margin-bottom: 24px; }
        .logo h1 { color: #1e3a5f; font-size: 22px; margin: 0; }
        .otp-box { text-align: center; background: #f0f4ff; border-radius: 8px; padding: 24px; margin: 24px 0; }
        .otp-code { font-size: 40px; font-weight: bold; letter-spacing: 10px; color: #1e3a5f; }
        .note { color: #666; font-size: 14px; text-align: center; }
        .footer { color: #999; font-size: 12px; text-align: center; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://www.kzs02.com/images/logo.jpg" alt="KZS 2002"
                style="height:52px; width:auto; display:block; margin:0 auto 8px;">
            <p style="color:#666; margin:4px 0 0; font-size:13px;">Kushtia Zilla School — 25-Year Reunion</p>
        </div>

        <p>Please use the verification code below to complete your registration:</p>

        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <p class="note">This code expires in <strong>15 minutes</strong>. Do not share it with anyone.</p>

        <p class="note" style="margin-top: 16px;">
            If you did not register on the KZS 2002 Reunion portal, please ignore this email.
        </p>

        <div class="footer">
            KZS 2002 SSC Batch Reunion Committee
        </div>
    </div>
</body>
</html>
