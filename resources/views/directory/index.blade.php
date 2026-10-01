@extends('layouts.app')
@section('title', 'Alumni Directory — KZS 2002 Reunion')

@section('content')

<div class="mb-6 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-bold text-primary">Alumni Directory</h1>
    <p class="text-gray-500 dark:text-gray-400 text-sm">KZS 2002 SSC Batch — All Verified Batchmates</p>
  </div>
  <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary transition">&larr; Dashboard</a>
</div>

{{-- Stats strip --}}
<div class="grid grid-cols-3 gap-3 mb-6">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
    <p class="text-2xl font-bold text-kgray dark:text-gray-100">{{ $totalCount }}</p>
    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide font-semibold mt-0.5">Total Batchmates</p>
  </div>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-green-100 dark:border-gray-700 p-4 text-center">
    <p class="text-2xl font-bold text-kgreen">{{ $comingCount }}</p>
    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide font-semibold mt-0.5">Coming to Reunion</p>
  </div>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
    <p class="text-2xl font-bold text-gray-400 dark:text-gray-500">{{ $totalCount - $comingCount }}</p>
    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide font-semibold mt-0.5">Not Yet Registered</p>
  </div>
</div>

{{-- Filter tabs + view toggle --}}
<div class="flex items-center justify-between gap-3 mb-5 flex-wrap">
  {{-- Filter tabs --}}
  <div class="overflow-x-auto flex-1">
    <div class="flex gap-2 min-w-max" id="filterTabs">
      <button onclick="filterDir('all')" data-filter="all"
        class="filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border-2 border-primary bg-primary text-white transition">
        All ({{ $totalCount }})
      </button>
      <button onclick="filterDir('coming')" data-filter="coming"
        class="filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-kgreen hover:text-kgreen transition">
        Coming ✅ ({{ $comingCount }})
      </button>
      <button onclick="filterDir('not')" data-filter="not"
        class="filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-gray-400 transition">
        Not Registered ({{ $totalCount - $comingCount }})
      </button>
    </div>
  </div>

  {{-- View toggle --}}
  <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1 flex-shrink-0">
    <button id="btnGrid" onclick="setView('grid')" title="Grid view"
      class="view-btn w-8 h-8 flex items-center justify-center rounded-md transition bg-white dark:bg-gray-600 shadow-sm text-primary">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
      </svg>
    </button>
    <button id="btnList" onclick="setView('list')" title="List view"
      class="view-btn w-8 h-8 flex items-center justify-center rounded-md transition text-gray-400 dark:text-gray-500">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M3 4a1 1 0 000 2h14a1 1 0 100-2H3zm0 4a1 1 0 000 2h14a1 1 0 100-2H3zm0 4a1 1 0 000 2h14a1 1 0 100-2H3z" clip-rule="evenodd"/>
      </svg>
    </button>
  </div>
</div>

@if($alumni->isEmpty())
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
    <div class="text-4xl mb-3">🏫</div>
    <p class="text-gray-500 dark:text-gray-400">No verified alumni yet.</p>
  </div>
@else

  {{-- ── GRID VIEW ──────────────────────────────────────── --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="alumniGrid">
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
    <div class="alumni-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border transition hover:shadow-md {{ $coming ? 'border-gray-100 dark:border-gray-700' : 'border-gray-100 dark:border-gray-700 opacity-75' }}"
         data-status="{{ $coming ? 'coming' : 'not' }}">

      <div class="p-4 text-center">
        {{-- Photo --}}
        <div class="relative w-16 h-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 mx-auto mb-3 flex items-center justify-center ring-2 {{ $coming ? 'ring-kgreen' : 'ring-gray-200 dark:ring-gray-600' }}">
          @if($photoSrc)
            <img src="{{ $photoSrc }}" alt="{{ $person->name }}" class="w-full h-full object-cover">
          @else
            <span class="text-2xl font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($person->name, 0, 1)) }}</span>
          @endif
          @if($coming)
          <span class="absolute bottom-0 right-0 w-5 h-5 bg-kgreen rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center">
            <i class="fa-solid fa-check text-white" style="font-size:8px"></i>
          </span>
          @endif
        </div>

        <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm leading-tight">{{ $person->name }}</h3>

        @if($person->name_bn)
          <p class="text-gray-400 dark:text-gray-500 text-xs mt-0.5" style="font-family:'Tiro Bangla',serif">{{ $person->name_bn }}</p>
        @endif

        @if($person->designation && $person->organization)
          <p class="text-gray-500 dark:text-gray-400 text-xs mt-1 leading-tight">{{ $person->designation }}<br><span class="text-gray-400 dark:text-gray-500">{{ $person->organization }}</span></p>
        @elseif($person->current_profession)
          <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $person->current_profession }}</p>
        @endif

        @if($person->current_location)
          <p class="text-gray-400 dark:text-gray-500 text-xs mt-0.5">📍 {{ $person->current_location }}</p>
        @endif

        {{-- Mobile & Email --}}
        @if($person->mobile)
          <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">📞 {{ $person->mobile }}</p>
        @endif
        @if($person->email)
          <p class="text-gray-400 dark:text-gray-500 text-xs mt-0.5 break-all">✉️ {{ $person->email }}</p>
        @endif
      </div>

      {{-- Event status footer --}}
      <div class="border-t border-gray-100 dark:border-gray-700 px-3 py-2 text-center">
        @if($coming)
          <div class="space-y-1">
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-kgreen">
              <i class="fa-solid fa-circle-check"></i> Coming to Reunion
            </span>
            <div>
              @if($paid)
                <span class="inline-block text-[11px] font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">✅ Paid</span>
              @elseif($pending)
                <span class="inline-block text-[11px] font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">⏳ Pending</span>
              @else
                <span class="inline-block text-[11px] font-semibold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">💳 Unpaid</span>
              @endif
            </div>
            @if($reg->guest_count > 0)
              <p class="text-gray-400 dark:text-gray-500 text-xs">+{{ $reg->guest_count }} guest{{ $reg->guest_count > 1 ? 's' : '' }}</p>
            @endif
          </div>
        @else
          <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-400 dark:text-gray-500">
            <i class="fa-regular fa-clock"></i> Not Yet Registered
          </span>
        @endif
      </div>
    </div>
    @endforeach
  </div>

  {{-- ── LIST VIEW ──────────────────────────────────────── --}}
  <div class="hidden flex-col gap-3" id="alumniList">
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
    <div class="alumni-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md {{ $coming ? '' : 'opacity-75' }}"
         data-status="{{ $coming ? 'coming' : 'not' }}">
      <div class="flex items-center gap-4 p-4">

        {{-- Photo --}}
        <div class="relative w-14 h-14 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0 flex items-center justify-center ring-2 {{ $coming ? 'ring-kgreen' : 'ring-gray-200 dark:ring-gray-600' }}">
          @if($photoSrc)
            <img src="{{ $photoSrc }}" alt="{{ $person->name }}" class="w-full h-full object-cover">
          @else
            <span class="text-xl font-bold text-gray-400 dark:text-gray-500">{{ strtoupper(substr($person->name, 0, 1)) }}</span>
          @endif
          @if($coming)
          <span class="absolute bottom-0 right-0 w-4 h-4 bg-kgreen rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center">
            <i class="fa-solid fa-check text-white" style="font-size:7px"></i>
          </span>
          @endif
        </div>

        {{-- Main info --}}
        <div class="flex-1 min-w-0">
          <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $person->name }}</h3>
            @if($person->name_bn)
              <span class="text-gray-400 dark:text-gray-500 text-xs" style="font-family:'Tiro Bangla',serif">{{ $person->name_bn }}</span>
            @endif
          </div>

          <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400">
            @if($person->designation && $person->organization)
              <span>{{ $person->designation }}, {{ $person->organization }}</span>
            @elseif($person->current_profession)
              <span>{{ $person->current_profession }}</span>
            @endif
            @if($person->current_location)
              <span>📍 {{ $person->current_location }}</span>
            @endif
          </div>

          <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-400 dark:text-gray-500">
            @if($person->mobile)
              <span>📞 {{ $person->mobile }}</span>
            @endif
            @if($person->email)
              <span>✉️ {{ $person->email }}</span>
            @endif
          </div>
        </div>

        {{-- Event status --}}
        <div class="flex-shrink-0 text-right">
          @if($coming)
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-kgreen">
              <i class="fa-solid fa-circle-check"></i> Coming
            </span>
            <div class="mt-1">
              @if($paid)
                <span class="inline-block text-[11px] font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">✅ Paid</span>
              @elseif($pending)
                <span class="inline-block text-[11px] font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">⏳ Pending</span>
              @else
                <span class="inline-block text-[11px] font-semibold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">💳 Unpaid</span>
              @endif
            </div>
            @if($reg->guest_count > 0)
              <p class="text-gray-400 dark:text-gray-500 text-xs mt-0.5">+{{ $reg->guest_count }} guest{{ $reg->guest_count > 1 ? 's' : '' }}</p>
            @endif
          @else
            <span class="text-xs text-gray-400 dark:text-gray-500">Not Registered</span>
          @endif
        </div>

      </div>
    </div>
    @endforeach
  </div>

@endif

<script>
  var currentFilter = 'all';

  function filterDir(filter) {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(btn => {
      const active = btn.dataset.filter === filter;
      btn.className = active
        ? 'filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border-2 border-primary bg-primary text-white transition'
        : 'filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-kgreen hover:text-kgreen transition';
    });
    applyFilter();
  }

  function applyFilter() {
    document.querySelectorAll('.alumni-card').forEach(card => {
      const show = currentFilter === 'all' || card.dataset.status === currentFilter;
      card.style.display = show ? '' : 'none';
    });
  }

  function setView(mode) {
    const grid = document.getElementById('alumniGrid');
    const list = document.getElementById('alumniList');
    const btnGrid = document.getElementById('btnGrid');
    const btnList = document.getElementById('btnList');

    if (mode === 'grid') {
      grid.classList.remove('hidden');
      list.classList.add('hidden');
      list.classList.remove('flex');
      btnGrid.classList.add('bg-white', 'dark:bg-gray-600', 'shadow-sm', 'text-primary');
      btnGrid.classList.remove('text-gray-400', 'dark:text-gray-500');
      btnList.classList.remove('bg-white', 'dark:bg-gray-600', 'shadow-sm', 'text-primary');
      btnList.classList.add('text-gray-400', 'dark:text-gray-500');
    } else {
      list.classList.remove('hidden');
      list.classList.add('flex');
      grid.classList.add('hidden');
      btnList.classList.add('bg-white', 'dark:bg-gray-600', 'shadow-sm', 'text-primary');
      btnList.classList.remove('text-gray-400', 'dark:text-gray-500');
      btnGrid.classList.remove('bg-white', 'dark:bg-gray-600', 'shadow-sm', 'text-primary');
      btnGrid.classList.add('text-gray-400', 'dark:text-gray-500');
    }

    localStorage.setItem('dirView', mode);
    applyFilter();
  }

  // Restore saved view
  (function() {
    var saved = localStorage.getItem('dirView') || 'grid';
    setView(saved);
  })();
</script>

@endsection
