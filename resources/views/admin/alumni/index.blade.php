@extends('layouts.admin')
@section('title', 'Alumni')
@section('heading', 'Alumni Management')

@section('content')

{{-- Tabs + search --}}
<div style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:16px">
  <div style="display:flex;gap:6px;flex-wrap:wrap">
    @foreach(['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
    <a href="{{ route('admin.alumni.index', ['status' => $key, 'search' => request('search')]) }}"
      style="display:inline-block;padding:6px 14px;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;transition:.15s;
      {{ $status === $key ? 'background:var(--red-700);color:#fff;' : 'background:#fff;color:var(--muted);border:1px solid var(--line);' }}">
      {{ $label }}
      <span style="opacity:.7;font-size:12px">({{ $counts[$key] }})</span>
    </a>
    @endforeach
  </div>

  <form method="GET" action="{{ route('admin.alumni.index') }}" style="display:flex;gap:8px;margin-left:auto">
    <input type="hidden" name="status" value="{{ $status }}">
    <input type="text" name="search" value="{{ request('search') }}"
      placeholder="Search name, roll, email…" style="width:200px">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </form>
</div>

{{-- Table --}}
<div class="tbl-wrap">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Roll</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Joined</th>
        <th style="text-align:right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($alumni as $a)
      <tr>
        <td style="font-weight:500;color:var(--ink)">
          <a href="{{ route('admin.alumni.show', $a) }}" style="color:var(--red-700);text-decoration:none">
            {{ $a->name }}
          </a>
          @if($a->admin_notes_count > 0)
            <span class="badge badge-yellow" style="margin-left:6px;font-size:10px">
              {{ $a->admin_notes_count }} note{{ $a->admin_notes_count > 1 ? 's' : '' }}
            </span>
          @endif
        </td>
        <td style="color:var(--muted)">{{ $a->roll_number }}</td>
        <td style="color:var(--muted)">{{ $a->email }}</td>
        <td style="color:var(--muted)">{{ $a->phone ?: '—' }}</td>
        <td>
          <span class="badge {{ $a->status === 'verified' ? 'badge-green' : ($a->status === 'rejected' ? 'badge-red' : 'badge-yellow') }}">
            {{ ucfirst($a->status) }}
          </span>
        </td>
        <td style="color:var(--muted);font-size:12px">{{ $a->created_at->format('d M Y') }}</td>
        <td style="text-align:right">
          <div style="display:flex;justify-content:flex-end;gap:6px">
            @if($a->status !== 'verified')
            <form method="POST" action="{{ route('admin.alumni.verify', $a) }}">
              @csrf
              <button class="btn btn-sm" style="background:#dcfce7;color:#166534;border-color:#86efac">✓ Verify</button>
            </form>
            @endif
            @if($a->status !== 'rejected')
            <form method="POST" action="{{ route('admin.alumni.reject', $a) }}">
              @csrf
              <button class="btn btn-sm" style="background:#fee2e2;color:#991b1b;border-color:#fca5a5"
                onclick="return confirm('Reject {{ $a->name }}?')">✕ Reject</button>
            </form>
            @endif
            <a href="{{ route('admin.alumni.show', $a) }}" class="btn btn-ghost btn-sm">View</a>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="7" style="text-align:center;color:var(--muted);padding:32px">No alumni found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  @if($alumni->hasPages())
  <div style="padding:12px 16px;border-top:1px solid var(--tint)">
    {{ $alumni->links() }}
  </div>
  @endif
</div>

@endsection
