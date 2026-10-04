@extends('layouts.app')
@section('title', 'অ্যালামনাই ডিরেক্টরি — KZS 2002')

@section('content')
<div class="m-main">
<div class="wrap">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
    <div>
      <h1 class="m-title" style="font-size:22px" data-en="Alumni Directory">অ্যালামনাই ডিরেক্টরি</h1>
      <p style="color:var(--muted);font-size:13px" data-en="KZS 2002 SSC Batch — All Verified Batchmates">KZS ২০০২ SSC ব্যাচ — সকল যাচাইকৃত বন্ধু</p>
    </div>
    <a href="{{ route('dashboard') }}" style="font-size:13px;color:var(--muted);text-decoration:none" data-en="← Dashboard">← ড্যাশবোর্ড</a>
  </div>

  {{-- Stats strip --}}
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px">
    <div class="panel" style="text-align:center;padding:16px">
      <p style="font-size:24px;font-weight:700;color:var(--ink)">{{ $totalCount }}</p>
      <p style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-top:2px" data-en="Total Batchmates">মোট বন্ধু</p>
    </div>
    <div class="panel" style="text-align:center;padding:16px;border-color:#bbf7d0">
      <p style="font-size:24px;font-weight:700;color:#16a34a">{{ $comingCount }}</p>
      <p style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-top:2px" data-en="Coming to Reunion">রিইউনিয়নে আসছেন</p>
    </div>
    <div class="panel" style="text-align:center;padding:16px">
      <p style="font-size:24px;font-weight:700;color:var(--muted)">{{ $totalCount - $comingCount }}</p>
      <p style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-top:2px" data-en="Not Yet Registered">রেজিস্ট্রেশন বাকি</p>
    </div>
  </div>

  {{-- Filter tabs + view toggle --}}
  <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;flex-wrap:wrap">
    <div style="overflow-x:auto;flex:1">
      <div style="display:flex;gap:8px;min-width:max-content" id="filterTabs">
        <button onclick="filterDir('all')" data-filter="all" id="fb-all"
          style="padding:6px 16px;border-radius:99px;font-size:13px;font-weight:600;border:2px solid var(--red-700);background:var(--red-700);color:#fff;cursor:pointer;transition:.15s" data-en="All">
          সব ({{ $totalCount }})
        </button>
        <button onclick="filterDir('coming')" data-filter="coming" id="fb-coming"
          style="padding:6px 16px;border-radius:99px;font-size:13px;font-weight:600;border:2px solid var(--line);background:transparent;color:var(--muted);cursor:pointer;transition:.15s" data-en="Coming ✅">
          আসছেন ✅ ({{ $comingCount }})
        </button>
        <button onclick="filterDir('not')" data-filter="not" id="fb-not"
          style="padding:6px 16px;border-radius:99px;font-size:13px;font-weight:600;border:2px solid var(--line);background:transparent;color:var(--muted);cursor:pointer;transition:.15s" data-en="Not Registered">
          রেজিস্ট্রেশন বাকি ({{ $totalCount - $comingCount }})
        </button>
      </div>
    </div>

    {{-- View toggle --}}
    <div style="display:flex;align-items:center;gap:4px;background:var(--tint);border-radius:10px;padding:4px;flex-shrink:0">
      <button id="btnGrid" onclick="setView('grid')" title="Grid view"
        style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:7px;border:none;cursor:pointer;background:var(--surface);box-shadow:0 1px 3px rgba(0,0,0,.08);color:var(--red-700)">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
          <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
      </button>
      <button id="btnList" onclick="setView('list')" title="List view"
        style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:7px;border:none;cursor:pointer;background:transparent;color:var(--muted)">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3 4a1 1 0 000 2h14a1 1 0 100-2H3zm0 4a1 1 0 000 2h14a1 1 0 100-2H3zm0 4a1 1 0 000 2h14a1 1 0 100-2H3z" clip-rule="evenodd"/>
        </svg>
      </button>
    </div>
  </div>

@if($alumni->isEmpty())
  <div class="panel" style="text-align:center;padding:48px 24px">
    <div style="font-size:40px;margin-bottom:12px">🏫</div>
    <p style="color:var(--muted)" data-en="No verified alumni yet.">এখনো কোনো যাচাইকৃত অ্যালামনাই নেই।</p>
  </div>
@else

  {{-- GRID VIEW --}}
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:14px" id="alumniGrid">
    @foreach($alumni as $person)
    @php
      $reg     = $person->eventRegistration;
      $coming  = (bool) $reg;
      $paid    = $reg?->payment_status === 'paid';
      $pending = $reg?->payment_status === 'pending';
      $photoSrc = $person->photo_url
        ? (str_starts_with($person->photo_url, 'uploads/') ? asset($person->photo_url) : asset('storage/' . $person->photo_url))
        : null;
    @endphp
    <div class="panel alumni-card" style="padding:0;overflow:hidden;{{ $coming ? '' : 'opacity:.8' }}" data-status="{{ $coming ? 'coming' : 'not' }}">
      <div style="padding:16px;text-align:center">
        <div style="position:relative;width:60px;height:60px;border-radius:50%;overflow:hidden;background:var(--tint);margin:0 auto 12px;display:flex;align-items:center;justify-content:center;border:2px solid {{ $coming ? '#16a34a' : 'var(--line)' }}">
          @if($photoSrc)
            <img src="{{ $photoSrc }}" alt="{{ $person->name }}" style="width:100%;height:100%;object-fit:cover">
          @else
            <span style="font-size:22px;font-weight:700;color:var(--red-700);font-family:var(--f-display)">{{ strtoupper(substr($person->name, 0, 1)) }}</span>
          @endif
          @if($coming)
          <span style="position:absolute;bottom:0;right:0;width:16px;height:16px;background:#16a34a;border-radius:50%;border:2px solid var(--surface);display:flex;align-items:center;justify-content:center">
            <i class="fa-solid fa-check" style="color:#fff;font-size:7px"></i>
          </span>
          @endif
        </div>
        <h3 style="font-size:13px;font-weight:600;color:var(--ink);line-height:1.3">{{ $person->name }}</h3>
        @if($person->name_bn)
          <p style="color:var(--muted);font-size:12px;margin-top:2px;font-family:var(--f-body)">{{ $person->name_bn }}</p>
        @endif
        @if($person->designation && $person->organization)
          <p style="color:var(--muted);font-size:11px;margin-top:6px;line-height:1.4">{{ $person->designation }}<br><span style="color:var(--muted)">{{ $person->organization }}</span></p>
        @elseif($person->current_profession)
          <p style="color:var(--muted);font-size:11px;margin-top:6px">{{ $person->current_profession }}</p>
        @endif
        @if($person->current_location)
          <p style="color:var(--muted);font-size:11px;margin-top:4px">📍 {{ $person->current_location }}</p>
        @endif
        @if($person->mobile)
          <p style="color:var(--muted);font-size:11px;margin-top:4px">📞 {{ $person->mobile }}</p>
        @endif
        @if($person->email)
          <p style="color:var(--muted);font-size:11px;margin-top:2px;word-break:break-all">✉️ {{ $person->email }}</p>
        @endif
      </div>
      <div style="border-top:1px solid var(--line);padding:8px 12px;text-align:center">
        @if($coming)
          <span style="font-size:11px;font-weight:600;color:#16a34a;display:block">
            <i class="fa-solid fa-circle-check"></i> <span data-en="Coming to Reunion">রিইউনিয়নে আসছেন</span>
          </span>
          <div style="margin-top:4px">
            @if($paid)
              <span class="badge badge-green" style="font-size:10px">✅ <span data-en="Paid">পেমেন্ট হয়েছে</span></span>
            @elseif($pending)
              <span class="badge badge-blue" style="font-size:10px">⏳ <span data-en="Pending">যাচাইয়ে আছে</span></span>
            @else
              <span class="badge badge-yellow" style="font-size:10px">💳 <span data-en="Unpaid">পেমেন্ট বাকি</span></span>
            @endif
          </div>
          @if($reg->guest_count > 0)
            <p style="color:var(--muted);font-size:11px;margin-top:2px">+{{ $reg->guest_count }} <span data-en="guest(s)">গেস্ট</span></p>
          @endif
        @else
          <span style="font-size:11px;color:var(--muted)">
            <i class="fa-regular fa-clock"></i> <span data-en="Not Yet Registered">রেজিস্ট্রেশন বাকি</span>
          </span>
        @endif
      </div>
    </div>
    @endforeach
  </div>

  {{-- LIST VIEW --}}
  <div style="display:none;flex-direction:column;gap:10px" id="alumniList">
    @foreach($alumni as $person)
    @php
      $reg     = $person->eventRegistration;
      $coming  = (bool) $reg;
      $paid    = $reg?->payment_status === 'paid';
      $pending = $reg?->payment_status === 'pending';
      $photoSrc = $person->photo_url
        ? (str_starts_with($person->photo_url, 'uploads/') ? asset($person->photo_url) : asset('storage/' . $person->photo_url))
        : null;
    @endphp
    <div class="panel alumni-card" style="{{ $coming ? '' : 'opacity:.8' }}" data-status="{{ $coming ? 'coming' : 'not' }}">
      <div style="display:flex;align-items:center;gap:14px">
        <div style="position:relative;width:52px;height:52px;border-radius:50%;overflow:hidden;background:var(--tint);flex-shrink:0;display:flex;align-items:center;justify-content:center;border:2px solid {{ $coming ? '#16a34a' : 'var(--line)' }}">
          @if($photoSrc)
            <img src="{{ $photoSrc }}" alt="{{ $person->name }}" style="width:100%;height:100%;object-fit:cover">
          @else
            <span style="font-size:20px;font-weight:700;color:var(--red-700);font-family:var(--f-display)">{{ strtoupper(substr($person->name, 0, 1)) }}</span>
          @endif
          @if($coming)
          <span style="position:absolute;bottom:0;right:0;width:14px;height:14px;background:#16a34a;border-radius:50%;border:2px solid var(--surface);display:flex;align-items:center;justify-content:center">
            <i class="fa-solid fa-check" style="color:#fff;font-size:6px"></i>
          </span>
          @endif
        </div>
        <div style="flex:1;min-width:0">
          <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px">
            <h3 style="font-size:14px;font-weight:600;color:var(--ink)">{{ $person->name }}</h3>
            @if($person->name_bn)
              <span style="font-size:12px;color:var(--muted);font-family:var(--f-body)">{{ $person->name_bn }}</span>
            @endif
          </div>
          <div style="margin-top:4px;display:flex;flex-wrap:wrap;gap:12px;font-size:12px;color:var(--muted)">
            @if($person->designation && $person->organization)
              <span>{{ $person->designation }}, {{ $person->organization }}</span>
            @elseif($person->current_profession)
              <span>{{ $person->current_profession }}</span>
            @endif
            @if($person->current_location)
              <span>📍 {{ $person->current_location }}</span>
            @endif
          </div>
          <div style="margin-top:2px;display:flex;flex-wrap:wrap;gap:12px;font-size:12px;color:var(--muted)">
            @if($person->mobile)
              <span>📞 {{ $person->mobile }}</span>
            @endif
            @if($person->email)
              <span>✉️ {{ $person->email }}</span>
            @endif
          </div>
        </div>
        <div style="flex-shrink:0;text-align:right">
          @if($coming)
            <span style="font-size:12px;font-weight:600;color:#16a34a;display:block">
              <i class="fa-solid fa-circle-check"></i> <span data-en="Coming">আসছেন</span>
            </span>
            <div style="margin-top:4px">
              @if($paid)
                <span class="badge badge-green" style="font-size:10px">✅ <span data-en="Paid">পেমেন্ট হয়েছে</span></span>
              @elseif($pending)
                <span class="badge badge-blue" style="font-size:10px">⏳ <span data-en="Pending">যাচাইয়ে আছে</span></span>
              @else
                <span class="badge badge-yellow" style="font-size:10px">💳 <span data-en="Unpaid">পেমেন্ট বাকি</span></span>
              @endif
            </div>
            @if($reg->guest_count > 0)
              <p style="color:var(--muted);font-size:11px;margin-top:2px">+{{ $reg->guest_count }} <span data-en="guest(s)">গেস্ট</span></p>
            @endif
          @else
            <span style="font-size:12px;color:var(--muted)" data-en="Not Registered">রেজিস্ট্রেশন বাকি</span>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>

@endif

</div>
</div>

<script>
var currentFilter = 'all';

function filterDir(filter) {
  currentFilter = filter;
  ['all','coming','not'].forEach(function(f) {
    var btn = document.getElementById('fb-' + f);
    if (!btn) return;
    if (f === filter) {
      btn.style.background = 'var(--red-700)';
      btn.style.borderColor = 'var(--red-700)';
      btn.style.color = '#fff';
    } else {
      btn.style.background = 'transparent';
      btn.style.borderColor = 'var(--line)';
      btn.style.color = 'var(--muted)';
    }
  });
  applyFilter();
}

function applyFilter() {
  document.querySelectorAll('.alumni-card').forEach(function(card) {
    var show = currentFilter === 'all' || card.dataset.status === currentFilter;
    card.style.display = show ? '' : 'none';
  });
}

function setView(mode) {
  var grid = document.getElementById('alumniGrid');
  var list = document.getElementById('alumniList');
  var btnGrid = document.getElementById('btnGrid');
  var btnList = document.getElementById('btnList');

  if (mode === 'grid') {
    grid.style.display = 'grid';
    list.style.display = 'none';
    btnGrid.style.background = 'var(--surface)';
    btnGrid.style.color = 'var(--red-700)';
    btnGrid.style.boxShadow = '0 1px 3px rgba(0,0,0,.08)';
    btnList.style.background = 'transparent';
    btnList.style.color = 'var(--muted)';
    btnList.style.boxShadow = 'none';
  } else {
    list.style.display = 'flex';
    grid.style.display = 'none';
    btnList.style.background = 'var(--surface)';
    btnList.style.color = 'var(--red-700)';
    btnList.style.boxShadow = '0 1px 3px rgba(0,0,0,.08)';
    btnGrid.style.background = 'transparent';
    btnGrid.style.color = 'var(--muted)';
    btnGrid.style.boxShadow = 'none';
  }

  localStorage.setItem('dirView', mode);
  applyFilter();
}

(function() {
  var saved = localStorage.getItem('dirView') || 'grid';
  setView(saved);
  filterDir('all');
})();
</script>

@endsection
