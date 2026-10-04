@extends('layouts.admin')
@section('title', 'Transactions')
@section('heading', 'Payment History')

@section('content')

{{-- Summary --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px;margin-bottom:20px">
  @foreach([
    ['Total',     $counts['all'],       '#374151', '#f3f4f6', ''],
    ['Success',   $counts['success'],   '#166534', '#dcfce7', '#86efac'],
    ['Failed',    $counts['failed'],    '#991b1b', '#fee2e2', '#fca5a5'],
    ['Initiated', $counts['initiated'], '#854d0e', '#fef9c3', '#fcd34d'],
    ['Cancelled', $counts['cancelled'], '#4b5563', '#f3f4f6', ''],
  ] as [$label, $count, $color, $bg, $border])
  <div class="panel" style="padding:14px;{{ $border ? 'border-color:'.$border.';' : '' }}">
    <p style="font-size:11px;color:{{ $color }};margin-bottom:4px">{{ $label }}</p>
    <p style="font-size:24px;font-weight:700;color:{{ $color }}">{{ $count }}</p>
  </div>
  @endforeach
</div>

{{-- Filters --}}
<div class="panel" style="margin-bottom:16px">
  <form method="GET" action="{{ route('admin.transactions.index') }}" style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end">
    <div>
      <label style="display:block;font-size:12px;color:var(--muted);margin-bottom:4px">Status</label>
      <select name="status">
        <option value="">All</option>
        <option value="success"   {{ request('status') === 'success'   ? 'selected' : '' }}>Success</option>
        <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Failed</option>
        <option value="initiated" {{ request('status') === 'initiated' ? 'selected' : '' }}>Initiated</option>
        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
      </select>
    </div>
    <div>
      <label style="display:block;font-size:12px;color:var(--muted);margin-bottom:4px">Search Alumni</label>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email…" style="width:180px">
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="{{ route('admin.transactions.index') }}" style="font-size:13px;color:var(--muted);text-decoration:none;align-self:center">Reset</a>
  </form>
</div>

{{-- Table --}}
<div class="tbl-wrap">
  <table>
    <thead>
      <tr>
        <th>Alumni</th>
        <th>Tran ID</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Gateway Ref</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      @forelse($transactions as $txn)
      <tr>
        <td>
          @if($txn->alumni)
          <a href="{{ route('admin.alumni.show', $txn->alumni) }}"
            style="font-weight:500;color:var(--red-700);text-decoration:none">
            {{ $txn->alumni->name }}
          </a>
          <p style="font-size:11px;color:var(--muted)">{{ $txn->alumni->email }}</p>
          @else
          <span style="color:var(--muted);font-size:12px">Deleted</span>
          @endif
        </td>
        <td style="font-family:monospace;font-size:12px;color:var(--muted)">{{ $txn->gateway_transaction_id }}</td>
        <td style="font-weight:600;color:var(--ink)">৳{{ number_format($txn->amount, 0) }}</td>
        <td>
          @php
            $cls = match($txn->status) {
              'success'   => 'badge-green',
              'failed'    => 'badge-red',
              'initiated' => 'badge-yellow',
              'cancelled' => 'badge-gray',
              default     => 'badge-gray',
            };
          @endphp
          <span class="badge {{ $cls }}">{{ ucfirst($txn->status) }}</span>
        </td>
        <td style="font-family:monospace;font-size:12px;color:var(--muted)">{{ $txn->gateway_ref ?: '—' }}</td>
        <td style="font-size:12px;color:var(--muted)">{{ $txn->created_at->format('d M Y, H:i') }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="6" style="text-align:center;color:var(--muted);padding:32px">No transactions found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  @if($transactions->hasPages())
  <div style="padding:12px 16px;border-top:1px solid var(--tint)">
    {{ $transactions->links() }}
  </div>
  @endif
</div>

@endsection
