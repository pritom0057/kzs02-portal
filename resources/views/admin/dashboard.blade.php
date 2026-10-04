@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')

{{-- Stat cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px;margin-bottom:24px">
  @foreach([
    ['label' => 'Total Alumni',  'value' => $stats['total'],     'color' => '#374151', 'bg' => '#f3f4f6'],
    ['label' => 'Pending',       'value' => $stats['pending'],    'color' => '#854d0e', 'bg' => '#fef9c3'],
    ['label' => 'Verified',      'value' => $stats['verified'],   'color' => '#166534', 'bg' => '#dcfce7'],
    ['label' => 'Rejected',      'value' => $stats['rejected'],   'color' => '#991b1b', 'bg' => '#fee2e2'],
    ['label' => 'Event Reg.',    'value' => $stats['registered'], 'color' => '#1e40af', 'bg' => '#dbeafe'],
    ['label' => 'Paid',          'value' => $stats['paid'],       'color' => '#6b21a8', 'bg' => '#f3e8ff'],
  ] as $stat)
  <div class="panel" style="padding:14px">
    <p style="font-size:11px;color:var(--muted);margin-bottom:6px">{{ $stat['label'] }}</p>
    <p style="font-size:24px;font-weight:700;background:{{ $stat['bg'] }};color:{{ $stat['color'] }};display:inline-block;padding:2px 8px;border-radius:6px">
      {{ $stat['value'] }}
    </p>
  </div>
  @endforeach
</div>

{{-- Pending alumni queue --}}
<div class="panel" style="padding:0">
  <div style="padding:14px 20px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between">
    <h2 style="font-size:15px;font-weight:600;color:var(--ink)">Pending Approval</h2>
    <a href="{{ route('admin.alumni.index', ['status' => 'pending']) }}"
      style="font-size:12px;color:var(--red-700);text-decoration:none">View all &rarr;</a>
  </div>

  @if($pendingAlumni->isEmpty())
    <p style="padding:20px;font-size:13px;color:var(--muted)">No pending alumni. All caught up!</p>
  @else
    <ul style="list-style:none;padding:0;margin:0">
      @foreach($pendingAlumni as $a)
      <li style="padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--line)">
        <div style="min-width:0">
          <p style="font-size:14px;font-weight:500;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $a->name }}</p>
          <p style="font-size:12px;color:var(--muted)">Roll: {{ $a->roll_number }} · {{ $a->email }}</p>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0">
          <form method="POST" action="{{ route('admin.alumni.verify', $a) }}">
            @csrf
            <button class="btn btn-sm" style="background:#dcfce7;color:#166534;border-color:#86efac">✓ Verify</button>
          </form>
          <a href="{{ route('admin.alumni.show', $a) }}" class="btn btn-ghost btn-sm">View</a>
        </div>
      </li>
      @endforeach
    </ul>
  @endif
</div>

@endsection
