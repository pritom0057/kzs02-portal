@extends('layouts.admin')
@section('title', 'Registrations')
@section('heading', 'Event Registrations')

@section('content')

{{-- Summary --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:20px">
  <div class="panel" style="padding:14px">
    <p style="font-size:11px;color:var(--muted);margin-bottom:4px">Total Registered</p>
    <p style="font-size:24px;font-weight:700;color:var(--red-700)">{{ $registrations->total() }}</p>
  </div>
  <div class="panel" style="padding:14px;border-color:#93c5fd">
    <p style="font-size:11px;color:#1d4ed8;margin-bottom:4px">Pending Approval</p>
    <p style="font-size:24px;font-weight:700;color:#1d4ed8">{{ $pendingCount }}</p>
    <p style="font-size:11px;color:#1d4ed8">awaiting confirmation</p>
  </div>
  <div class="panel" style="padding:14px;border-color:#86efac">
    <p style="font-size:11px;color:var(--muted);margin-bottom:4px">Paid</p>
    <p style="font-size:24px;font-weight:700;color:#166534">{{ $paidCount }}</p>
  </div>
  <div class="panel" style="padding:14px">
    <p style="font-size:11px;color:var(--muted);margin-bottom:8px">T-Shirt Sizes</p>
    <div style="display:flex;flex-wrap:wrap;gap:4px">
      @foreach($tshirtSummary as $size => $count)
      <span class="badge badge-blue">{{ $size }}:{{ $count }}</span>
      @endforeach
    </div>
  </div>
</div>

{{-- Filters --}}
<div class="panel" style="margin-bottom:16px">
  <form method="GET" action="{{ route('admin.registrations.index') }}" style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end">
    <div>
      <label style="display:block;font-size:12px;color:var(--muted);margin-bottom:4px">Payment Status</label>
      <select name="payment_status">
        <option value="">All</option>
        <option value="unpaid"   {{ request('payment_status') === 'unpaid'   ? 'selected' : '' }}>Unpaid</option>
        <option value="pending"  {{ request('payment_status') === 'pending'  ? 'selected' : '' }}>Pending</option>
        <option value="paid"     {{ request('payment_status') === 'paid'     ? 'selected' : '' }}>Paid</option>
      </select>
    </div>
    <div>
      <label style="display:block;font-size:12px;color:var(--muted);margin-bottom:4px">T-Shirt Size</label>
      <select name="tshirt_size">
        <option value="">All</option>
        @foreach(['S','M','L','XL','XXL','XXXL'] as $size)
        <option value="{{ $size }}" {{ request('tshirt_size') === $size ? 'selected' : '' }}>{{ $size }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label style="display:block;font-size:12px;color:var(--muted);margin-bottom:4px">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or roll…" style="width:160px">
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="{{ route('admin.registrations.index') }}" style="font-size:13px;color:var(--muted);text-decoration:none;align-self:center">Reset</a>
    <a href="{{ route('admin.export.registrations') }}" class="btn btn-ghost btn-sm" style="margin-left:auto">↓ Export CSV</a>
  </form>
</div>

{{-- Table --}}
<div class="tbl-wrap">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>T-Shirt</th>
        <th>Guests</th>
        <th>Amount</th>
        <th>Method / Ref</th>
        <th>Payment</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($registrations as $reg)
      <tr style="{{ $reg->payment_status === 'pending' ? 'background:#eff6ff' : '' }}">
        <td>
          <a href="{{ route('admin.alumni.show', $reg->alumni) }}"
            style="font-weight:500;color:var(--red-700);text-decoration:none">
            {{ $reg->alumni->name }}
          </a>
          <p style="font-size:11px;color:var(--muted)">{{ $reg->alumni->roll_number ?: $reg->alumni->email }}</p>
        </td>
        <td>
          <span class="badge badge-blue">{{ $reg->tshirt_size }}</span>
        </td>
        <td style="color:var(--muted)">{{ $reg->guest_count }}</td>
        <td>
          <p style="font-weight:600;color:var(--ink)">৳{{ number_format($reg->total_amount) }}</p>
          @if($reg->paid_amount > 0 && $reg->paid_amount < $reg->total_amount)
          <p style="font-size:11px;color:#991b1b;font-weight:600;margin-top:2px">Due: ৳{{ number_format($reg->total_amount - $reg->paid_amount) }}</p>
          @elseif($reg->paid_amount >= $reg->total_amount && $reg->payment_status === 'paid')
          <p style="font-size:11px;color:#166534;margin-top:2px">Fully paid</p>
          @endif
        </td>
        <td>
          <p style="font-size:12px;color:var(--muted);text-transform:capitalize">{{ $reg->payment_method ?: '—' }}</p>
          @if($reg->payment_reference)
          <p style="font-size:11px;color:var(--muted);margin-top:2px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $reg->payment_reference }}">
            {{ $reg->payment_reference }}
          </p>
          @endif
        </td>
        <td>
          <span class="badge {{ $reg->payment_status === 'paid' ? 'badge-green' : ($reg->payment_status === 'pending' ? 'badge-blue' : 'badge-yellow') }}">
            {{ ucfirst($reg->payment_status ?? 'Unpaid') }}
          </span>
        </td>
        <td>
          @php
            $balanceDue = max(0, $reg->total_amount - $reg->paid_amount);
            $needsConfirm = $balanceDue > 0 || $reg->payment_status === 'pending';
          @endphp
          @if($needsConfirm)
          <form method="POST" action="{{ route('admin.alumni.payment.confirm', $reg->alumni) }}">
            @csrf
            <button class="btn btn-sm" style="background:#dcfce7;color:#166534;border-color:#86efac;white-space:nowrap">
              @if($reg->paid_amount > 0 && $balanceDue > 0)
                ✓ +৳{{ number_format($balanceDue) }}
              @else
                ✓ Confirm
              @endif
            </button>
          </form>
          @elseif($reg->paid_amount > $reg->total_amount)
          <form method="POST" action="{{ route('admin.alumni.payment.adjust', $reg->alumni) }}">
            @csrf
            <button class="btn btn-sm" style="background:#fef3c7;color:#b45309;border-color:#fcd34d;white-space:nowrap">⇅ Adjust</button>
          </form>
          @else
          <span style="font-size:12px;color:#166534;font-weight:600">✓ Paid</span>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="7" style="text-align:center;color:var(--muted);padding:32px">No registrations found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  @if($registrations->hasPages())
  <div style="padding:12px 16px;border-top:1px solid var(--tint)">
    {{ $registrations->links() }}
  </div>
  @endif
</div>

@endsection
