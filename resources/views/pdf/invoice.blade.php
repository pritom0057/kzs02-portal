<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Invoice — KZS 2002 Reunion</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1e293b; background: #fff; padding: 36px 40px; }

    /* Header */
    .header { display: table; width: 100%; margin-bottom: 28px; border-bottom: 3px solid #1e3a5f; padding-bottom: 20px; }
    .header-left { display: table-cell; vertical-align: middle; width: 60%; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; }
    .org-name { font-size: 16px; font-weight: bold; color: #1e3a5f; }
    .org-sub { font-size: 11px; color: #64748b; margin-top: 3px; }
    .invoice-label { font-size: 26px; font-weight: bold; color: #1e3a5f; letter-spacing: 2px; }
    .invoice-meta { font-size: 11px; color: #64748b; margin-top: 6px; line-height: 1.7; }

    /* Status stamp */
    .paid-stamp { display: inline-block; border: 3px solid #16a34a; color: #16a34a; font-size: 20px; font-weight: bold; letter-spacing: 4px; padding: 4px 14px; border-radius: 4px; opacity: .85; margin-top: 8px; }

    /* Bill-to */
    .section { margin-bottom: 22px; }
    .section-label { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 7px; }
    .bill-name { font-size: 14px; font-weight: bold; color: #1e293b; }
    .bill-sub { font-size: 11px; color: #64748b; margin-top: 3px; line-height: 1.6; }

    /* Table */
    table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
    thead th { background: #1e3a5f; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; padding: 9px 12px; text-align: left; }
    thead th:last-child { text-align: right; }
    tbody tr { border-bottom: 1px solid #f1f5f9; }
    tbody tr:last-child { border-bottom: none; }
    tbody td { padding: 10px 12px; font-size: 12px; vertical-align: top; }
    tbody td:last-child { text-align: right; font-weight: 600; }
    tfoot td { padding: 10px 12px; font-size: 12px; border-top: 2px solid #1e3a5f; }
    .total-row td { background: #f0fdf4; font-weight: bold; font-size: 13px; color: #166534; border-top: 2px solid #16a34a; }
    .total-row td:last-child { font-size: 15px; }

    /* Payment info box */
    .pay-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 16px; margin-top: 20px; }
    .pay-info-row { display: table; width: 100%; margin-bottom: 6px; }
    .pay-info-row:last-child { margin-bottom: 0; }
    .pi-label { display: table-cell; width: 140px; color: #64748b; font-size: 11px; }
    .pi-value { display: table-cell; font-weight: 600; font-size: 11px; color: #1e293b; }
    .pi-value.mono { font-family: DejaVu Sans Mono, monospace; }

    /* Footer */
    .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 10px; color: #94a3b8; line-height: 1.8; }
  </style>
</head>
<body>

@php
  $reg         = $registration;
  $method      = $reg->payment_method ?? 'manual';
  $methodLabel = match($method) {
    'bkash'         => 'bKash',
    'rocket'        => 'Rocket',
    'bank_transfer' => 'Bank Transfer (MTB)',
    'nagad'         => 'Nagad',
    default         => ucfirst($method),
  };
  $invoiceNo   = 'KZS2002-' . str_pad($reg->id, 5, '0', STR_PAD_LEFT);
  $confirmedAt = now()->format('d M Y');

  $bringSpouse  = (bool) $reg->bring_spouse;
  $driverIncl   = (bool) $reg->driver_included;
  $childrenList = is_array($reg->children_details) ? $reg->children_details : [];
  $guestsList   = is_array($reg->guests_details)   ? $reg->guests_details   : [];

  $lineItems = [];
  $lineItems[] = ['KZS 2002 Reunion — Member', 'Tk.2,002'];
  if ($bringSpouse)
    $lineItems[] = ['Spouse: ' . ($reg->spouse_name ?: '—'), 'Included'];
  foreach ($childrenList as $i => $c)
    $lineItems[] = ['Child ' . ($i+1) . ': ' . ($c['name'] ?? '—'), 'Included'];
  foreach ($guestsList as $i => $g)
    $lineItems[] = ['Guest ' . ($i+1) . ': ' . ($g['name'] ?? '—') . (!empty($g['relationship']) ? ' (' . $g['relationship'] . ')' : ''), 'Tk.1,000'];
  if ($driverIncl)
    $lineItems[] = ['Driver', 'Tk.500'];
  if (($reg->donation_amount ?? 0) > 0)
    $lineItems[] = ['Donation', 'Tk.' . number_format($reg->donation_amount)];
@endphp

{{-- Header --}}
<div class="header">
  <div class="header-left">
    <div class="org-name">Kushtia Zilla School Batch-2002</div>
    <div class="org-sub">KZS SSC 2002 Alumni Association<br>Silver Jubilee Reunion 2025</div>
  </div>
  <div class="header-right">
    <div class="invoice-label">INVOICE</div>
    <div class="invoice-meta">
      No: {{ $invoiceNo }}<br>
      Date: {{ $confirmedAt }}<br>
      Status: <strong style="color:#16a34a">PAID</strong>
    </div>
  </div>
</div>

{{-- Bill To --}}
<div class="section">
  <div class="section-label">Bill To</div>
  <div class="bill-name">{{ $alumnus->name }}</div>
  <div class="bill-sub">
    {{ $alumnus->email }}<br>
    @if($alumnus->mobile) {{ $alumnus->mobile }} @endif
  </div>
</div>

{{-- Line items --}}
<div class="section">
  <div class="section-label">Event Registration</div>
  <table>
    <thead>
      <tr>
        <th style="width:70%">Description</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
      @foreach($lineItems as $item)
      <tr>
        <td>{{ $item[0] }}</td>
        <td>{{ $item[1] }}</td>
      </tr>
      @endforeach
      @if($reg->tshirt_size)
      <tr>
        <td style="color:#64748b">T-Shirt Size</td>
        <td style="color:#64748b">{{ strtoupper($reg->tshirt_size) }}</td>
      </tr>
      @endif
    </tbody>
    <tfoot>
      <tr class="total-row">
        <td>Total Amount Paid</td>
        <td>Tk.{{ number_format($reg->total_amount) }}</td>
      </tr>
    </tfoot>
  </table>
</div>

{{-- Payment details --}}
<div class="pay-info">
  <div class="section-label" style="margin-bottom:10px">Payment Details</div>
  <div class="pay-info-row">
    <span class="pi-label">Payment Method</span>
    <span class="pi-value">{{ $methodLabel }}</span>
  </div>
  @if($reg->payment_reference)
  <div class="pay-info-row">
    <span class="pi-label">Transaction Reference</span>
    <span class="pi-value mono">{{ strtoupper($reg->payment_reference) }}</span>
  </div>
  @endif
  <div class="pay-info-row">
    <span class="pi-label">Confirmed On</span>
    <span class="pi-value">{{ $confirmedAt }}</span>
  </div>
  <div class="pay-info-row">
    <span class="pi-label">Amount Paid</span>
    <span class="pi-value" style="color:#166534;font-size:13px">Tk.{{ number_format($reg->total_amount) }}</span>
  </div>
</div>

{{-- Footer --}}
<div class="footer">
  Kushtia Zilla School Batch-2002 Reunion Committee &nbsp;|&nbsp; kzs02.com &nbsp;|&nbsp; admin@kzs02.com<br>
  This is a computer-generated invoice and does not require a signature.
</div>

</body>
</html>
