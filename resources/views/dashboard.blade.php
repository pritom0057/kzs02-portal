@extends('layouts.app')
@section('title', 'Dashboard — KZS 2002 Reunion')

@section('content')
@php
    $user = auth()->user();
    $reg  = $user->eventRegistration;

    $payStatus = $reg?->payment_status ?? null;
    $payLabel  = match($payStatus) {
        'paid'    => ['Paid',    'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
        'pending' => ['Pending', 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'],
        default   => ['Unpaid',  'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'],
    };
@endphp

{{-- ── HERO CARD ───────────────────────────────────────────── --}}
<div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 mb-6">

    {{-- Cover photo --}}
    <div class="h-28 sm:h-36 relative" style="background-image:url('/images/cover.jpg');background-size:cover;background-position:center;">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/75 via-red-700/65 to-red-900/55"></div>
        <div class="absolute top-3 right-4 text-white/40 text-4xl sm:text-5xl font-black select-none">KZS</div>
    </div>

    <div class="bg-white dark:bg-gray-800 px-5 pb-5 relative z-10">
        {{-- Avatar overlapping cover --}}
        @php
            $photoSrc = $user->photo_url
                ? (str_starts_with($user->photo_url, 'uploads/') ? asset($user->photo_url) : asset('storage/' . $user->photo_url))
                : null;
        @endphp
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div class="flex items-end gap-4 -mt-10 sm:-mt-12 relative z-20">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-primary flex items-center justify-center text-white text-3xl sm:text-4xl font-black shadow-lg ring-4 ring-white dark:ring-gray-800 shrink-0">
                    @if($photoSrc)
                        <img src="{{ $photoSrc }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="pb-1">
                    <h1 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100 leading-tight">{{ $user->name }}</h1>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">KZS · SSC Batch 2002</p>
                </div>
            </div>

            {{-- Edit profile button --}}
            <a href="{{ route('profile.edit') }}"
               class="self-start sm:self-end mb-1 inline-flex items-center gap-1.5 text-xs font-semibold border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-primary hover:text-primary dark:hover:border-primary dark:hover:text-primary px-3 py-1.5 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Edit Profile
            </a>
        </div>

        {{-- Status chips row --}}
        <div class="mt-4 flex flex-wrap gap-2">
            {{-- Account --}}
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700/50 px-3 py-1.5 rounded-full">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                Account Verified
            </span>

            {{-- Event Registration --}}
            @if($reg)
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700/50 px-3 py-1.5 rounded-full">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.75 1a.75.75 0 01.75.75V3h5V1.75a.75.75 0 011.5 0V3h.25A2.75 2.75 0 0117 5.75v11.5A2.75 2.75 0 0114.25 20H5.75A2.75 2.75 0 013 17.25V5.75A2.75 2.75 0 015.75 3H6V1.75A.75.75 0 016.75 1zM5.75 4.5c-.69 0-1.25.56-1.25 1.25v11.5c0 .69.56 1.25 1.25 1.25h8.5c.69 0 1.25-.56 1.25-1.25V5.75c0-.69-.56-1.25-1.25-1.25H5.75z"/></svg>
                Event Registered
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-700/50 px-3 py-1.5 rounded-full">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Not Registered
            </span>
            @endif

            {{-- Payment --}}
            @if($reg)
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $payLabel[1] }} border px-3 py-1.5 rounded-full
                {{ $payStatus === 'paid' ? 'border-green-200 dark:border-green-700/50' : ($payStatus === 'pending' ? 'border-blue-200 dark:border-blue-700/50' : 'border-orange-200 dark:border-orange-700/50') }}">
                @if($payStatus === 'paid')
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                @elseif($payStatus === 'pending')
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                @endif
                Payment {{ $payLabel[0] }}
            </span>
            @endif
        </div>
    </div>
</div>

{{-- ── QUICK ACTIONS ───────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Edit Profile --}}
    <a href="{{ route('profile.edit') }}"
       class="group flex items-center gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 hover:border-primary dark:hover:border-primary hover:shadow-md transition">
        <div class="w-11 h-11 rounded-xl bg-primary/10 dark:bg-primary/20 flex items-center justify-center shrink-0 group-hover:bg-primary/20 dark:group-hover:bg-primary/30 transition">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div class="min-w-0">
            <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm group-hover:text-primary transition leading-tight">Edit Profile</p>
            <p class="text-gray-400 dark:text-gray-500 text-xs mt-0.5 leading-snug">Photo, profession &amp; location</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-primary ml-auto shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>

    {{-- Event Registration --}}
    <a href="{{ route('event.show') }}"
       class="group flex items-center gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 hover:border-primary dark:hover:border-primary hover:shadow-md transition">
        <div class="w-11 h-11 rounded-xl bg-primary/10 dark:bg-primary/20 flex items-center justify-center shrink-0 group-hover:bg-primary/20 dark:group-hover:bg-primary/30 transition">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
        </div>
        <div class="min-w-0">
            <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm group-hover:text-primary transition leading-tight">
                {{ $reg ? 'Update Registration' : 'Register for Event' }}
            </p>
            <p class="text-gray-400 dark:text-gray-500 text-xs mt-0.5 leading-snug">Guests, T-shirt &amp; dietary notes</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-primary ml-auto shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>

    {{-- Alumni Directory --}}
    <a href="{{ route('directory') }}"
       class="group flex items-center gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 hover:border-primary dark:hover:border-primary hover:shadow-md transition">
        <div class="w-11 h-11 rounded-xl bg-primary/10 dark:bg-primary/20 flex items-center justify-center shrink-0 group-hover:bg-primary/20 dark:group-hover:bg-primary/30 transition">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="min-w-0">
            <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm group-hover:text-primary transition leading-tight">Alumni Directory</p>
            <p class="text-gray-400 dark:text-gray-500 text-xs mt-0.5 leading-snug">See who's coming to the reunion</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-primary ml-auto shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
</div>

{{-- ── PAYMENT CTA (only when registered but not paid) ──── --}}
@if($reg && $payStatus !== 'paid')
<div class="bg-gradient-to-r from-primary/5 to-orange-50 dark:from-primary/10 dark:to-orange-900/10 border border-orange-200 dark:border-orange-700/40 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center gap-3">
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <div>
            <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Payment {{ $payStatus === 'pending' ? 'Under Review' : 'Required' }}</p>
            <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">
                @if($payStatus === 'pending')
                    Your payment slip is being verified by the team.
                @else
                    Complete your payment to confirm your spot at the reunion.
                @endif
            </p>
        </div>
    </div>
    @if($payStatus !== 'pending')
    <a href="{{ route('event.show') }}"
       class="shrink-0 inline-flex items-center gap-1.5 bg-primary hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow-sm">
        Pay Now
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    @endif
</div>
@endif

@endsection
