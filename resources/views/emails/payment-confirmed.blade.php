<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment Confirmed — KZS 2002 Reunion</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f4f8; margin: 0; padding: 0; }
    .wrap { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .header { background: #1e3a5f; padding: 28px 36px; text-align: center; }
    .header img { height: 54px; display: block; margin: 0 auto 10px; }
    .header h1 { color: #fff; font-size: 17px; margin: 0; }
    .header p { color: #93c5fd; font-size: 12px; margin: 4px 0 0; }
    .body { padding: 32px 36px; }
    .check { text-align: center; font-size: 48px; margin-bottom: 8px; }
    .title { text-align: center; color: #166534; font-size: 20px; font-weight: bold; margin: 0 0 6px; }
    .subtitle { text-align: center; color: #64748b; font-size: 13px; margin: 0 0 28px; }
    .row { display: flex; justify-content: space-between; padding: 11px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    .row:last-child { border-bottom: none; }
    .lbl { color: #94a3b8; }
    .val { color: #1e293b; font-weight: 600; text-align: right; }
    .amount-row { background: #f0fdf4; border-radius: 8px; padding: 14px 16px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center; }
    .amount-row .lbl { color: #166534; font-weight: bold; font-size: 14px; }
    .amount-row .val { color: #166534; font-size: 22px; font-weight: bold; }
    .note { background: #f8fafc; border-radius: 8px; padding: 14px 16px; font-size: 12px; color: #64748b; margin-top: 20px; text-align: center; line-height: 1.6; }
    .footer { border-top: 1px solid #f1f5f9; padding: 18px 36px; text-align: center; color: #94a3b8; font-size: 11px; }
  </style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <img src="https://www.kzs02.com/images/logo.jpg" alt="KZS 2002">
    <h1>KZS 2002 Reunion</h1>
    <p>Kushtia Zilla School — 25-Year Reunion</p>
  </div>

  <div class="body">
    <div class="check">✅</div>
    <p class="title">{{ $isAdditional ? 'Additional Payment Confirmed' : 'Payment Confirmed' }}</p>
    <p class="subtitle">Dear {{ $alumnus->name }}, your payment has been verified by the admin.</p>

    @php
      $reg    = $registration;
      $method = $reg->payment_method ?? 'manual';
      $methodLabel = match($method) {
        'bkash'         => 'bKash',
        'rocket'        => 'Rocket',
        'bank_transfer' => 'Bank Transfer (MTB)',
        'nagad'         => 'Nagad',
        default         => ucfirst($method),
      };
    @endphp

    <div class="row"><span class="lbl">Name</span><span class="val">{{ $alumnus->name }}</span></div>
    <div class="row"><span class="lbl">Payment Method</span><span class="val">{{ $methodLabel }}</span></div>
    @if($reg->payment_reference)
    <div class="row"><span class="lbl">Transaction Ref</span><span class="val" style="font-family:monospace">{{ strtoupper($reg->payment_reference) }}</span></div>
    @endif
    <div class="row"><span class="lbl">Date Confirmed</span><span class="val">{{ now()->format('d M Y') }}</span></div>

    <div class="amount-row">
      <span class="lbl">Total Amount Paid</span>
      <span class="val">৳{{ number_format($reg->total_amount) }}</span>
    </div>

    <div class="note">
      Your invoice is attached to this email as a PDF.<br>
      Please save it as proof of your registration.
    </div>
  </div>

  <div class="footer">
    KZS 2002 SSC Batch Reunion Committee &nbsp;·&nbsp; kzs02.com<br>
    This is an automated email — please do not reply.
  </div>

</div>
</body>
</html>
