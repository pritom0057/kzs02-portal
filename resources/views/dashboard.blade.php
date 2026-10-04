@extends('layouts.app')
@section('title', 'ড্যাশবোর্ড — KZS 2002')

@section('content')
@php
  $user = auth()->user();
  $reg  = $user->eventRegistration;
  $payStatus = $reg?->payment_status ?? null;
  $photoSrc  = $user->photo_url
    ? (str_starts_with($user->photo_url, 'uploads/') ? asset($user->photo_url) : asset('storage/' . $user->photo_url))
    : null;
@endphp

<div class="m-main">
<div class="wrap">

{{-- ── PROFILE HERO CARD ── --}}
<div class="panel" style="padding:0;overflow:hidden;margin-bottom:20px">

  {{-- Cover --}}
  <div style="height:120px;background:linear-gradient(135deg,var(--red-950),var(--red-700));position:relative;overflow:hidden">
    <img src="{{ asset('images/cover.jpg') }}" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.55">
  </div>

  {{-- Avatar + info --}}
  <div style="padding:0 22px 22px">
    <div style="display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:12px">
      <div style="display:flex;align-items:flex-end;gap:16px;margin-top:-36px;position:relative;z-index:1">
        <div style="width:80px;height:80px;border-radius:18px;overflow:hidden;background:var(--red-700);display:flex;align-items:center;justify-content:center;color:#fff;font-size:32px;font-weight:800;font-family:var(--f-display);flex-shrink:0;border:4px solid #fff;box-shadow:var(--shadow)">
          @if($photoSrc)
            <img src="{{ $photoSrc }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover">
          @else
            {{ strtoupper(substr($user->name, 0, 1)) }}
          @endif
        </div>
        <div style="padding-bottom:2px;padding-top:48px;min-width:0">
          <h1 class="m-title" style="font-size:20px;word-break:break-word">{{ $user->name }}</h1>
          <p style="font-size:13px;color:var(--muted)">KZS · SSC ব্যাচ ২০০২</p>
        </div>
      </div>
      <a href="{{ route('profile.edit') }}" class="btn btn-ghost btn-sm" style="margin-bottom:4px">
        <i data-lucide="pencil" width="14" height="14"></i><span data-en="Edit Profile">প্রোফাইল সম্পাদনা</span>
      </a>
    </div>

    {{-- Status badges --}}
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:16px">
      <span class="badge badge-green"><i data-lucide="check-circle" width="12" height="12" style="display:inline;vertical-align:middle;margin-right:4px"></i><span data-en="Account Verified">অ্যাকাউন্ট যাচাইকৃত</span></span>

      @if($reg)
        <span class="badge badge-blue"><i data-lucide="calendar-check" width="12" height="12" style="display:inline;vertical-align:middle;margin-right:4px"></i><span data-en="Event Registered">ইভেন্ট রেজিস্ট্রেশন হয়েছে</span></span>
        @if($payStatus === 'paid')
          <span class="badge badge-green"><i data-lucide="credit-card" width="12" height="12" style="display:inline;vertical-align:middle;margin-right:4px"></i><span data-en="Payment Paid">পেমেন্ট সম্পন্ন</span></span>
        @elseif($payStatus === 'pending')
          <span class="badge badge-blue"><i data-lucide="clock" width="12" height="12" style="display:inline;vertical-align:middle;margin-right:4px"></i><span data-en="Payment Pending">পেমেন্ট যাচাইয়ে</span></span>
        @else
          <span class="badge badge-yellow"><i data-lucide="alert-circle" width="12" height="12" style="display:inline;vertical-align:middle;margin-right:4px"></i><span data-en="Payment Unpaid">পেমেন্ট বাকি</span></span>
        @endif
      @else
        <span class="badge badge-yellow"><i data-lucide="calendar-x" width="12" height="12" style="display:inline;vertical-align:middle;margin-right:4px"></i><span data-en="Not Registered">রেজিস্ট্রেশন হয়নি</span></span>
      @endif
    </div>
  </div>
</div>

{{-- ── QUICK ACTIONS ── --}}
<div class="stats-grid" style="margin-bottom:20px">
  <a href="{{ route('wall.index') }}" class="stat-card" style="text-decoration:none;display:block;transition:transform .15s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
    <div style="width:40px;height:40px;border-radius:12px;background:var(--tint);display:grid;place-items:center;margin-bottom:10px">
      <i data-lucide="layout-list" width="20" height="20" color="var(--red-700)"></i>
    </div>
    <p class="label" data-en="Batch Wall">ব্যাচ ওয়াল</p>
    <p style="font-size:13px;color:var(--muted)" data-en="Share news and memories">খবর ও স্মৃতি ভাগ করুন</p>
  </a>
  <a href="{{ route('profile.edit') }}" class="stat-card" style="text-decoration:none;display:block;transition:transform .15s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
    <div style="width:40px;height:40px;border-radius:12px;background:var(--tint);display:grid;place-items:center;margin-bottom:10px">
      <i data-lucide="user-round" width="20" height="20" color="var(--red-700)"></i>
    </div>
    <p class="label" data-en="My Profile">আমার প্রোফাইল</p>
    <p style="font-size:13px;color:var(--muted)" data-en="Photo, profession &amp; location">ছবি, পেশা ও ঠিকানা</p>
  </a>
  <a href="{{ route('event.show') }}" class="stat-card" style="text-decoration:none;display:block;transition:transform .15s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
    <div style="width:40px;height:40px;border-radius:12px;background:var(--tint);display:grid;place-items:center;margin-bottom:10px">
      <i data-lucide="ticket" width="20" height="20" color="var(--red-700)"></i>
    </div>
    <p class="label" data-en="Event Registration">ইভেন্ট রেজিস্ট্রেশন</p>
    <p style="font-size:13px;color:var(--muted)" data-en="{{ $reg ? 'Update your details' : 'Register for the reunion' }}">{{ $reg ? 'তথ্য আপডেট করুন' : 'রিইউনিয়নে রেজিস্ট্রেশন করুন' }}</p>
  </a>
  <a href="{{ route('directory') }}" class="stat-card" style="text-decoration:none;display:block;transition:transform .15s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
    <div style="width:40px;height:40px;border-radius:12px;background:var(--tint);display:grid;place-items:center;margin-bottom:10px">
      <i data-lucide="users" width="20" height="20" color="var(--red-700)"></i>
    </div>
    <p class="label" data-en="Alumni Directory">সদস্য তালিকা</p>
    <p style="font-size:13px;color:var(--muted)" data-en="See your batchmates">ব্যাচমেটদের দেখুন</p>
  </a>
</div>

{{-- ── PAYMENT CTA ── --}}
@if($reg && $payStatus !== 'paid')
<div style="background:var(--tint);border:1px solid var(--tint-2);border-radius:16px;padding:18px 20px;display:flex;flex-wrap:wrap;align-items:center;gap:14px">
  <div style="width:44px;height:44px;border-radius:14px;background:var(--red-700);display:grid;place-items:center;flex-shrink:0">
    <i data-lucide="credit-card" width="22" height="22" color="#fff"></i>
  </div>
  <div style="flex:1;min-width:0">
    <p style="font-weight:700;color:var(--red-900);font-family:var(--f-display)">
      @if($payStatus === 'pending')
        <span data-en="Payment Under Review">পেমেন্ট যাচাই হচ্ছে</span>
      @else
        <span data-en="Payment Required">পেমেন্ট বাকি আছে</span>
      @endif
    </p>
    <p style="font-size:13px;color:var(--muted)">
      @if($payStatus === 'pending')
        <span data-en="Your payment is being verified by the committee.">কমিটি আপনার পেমেন্ট যাচাই করছে।</span>
      @else
        <span data-en="Complete your payment to confirm your spot at the reunion.">রিইউনিয়নে আপনার আসন নিশ্চিত করতে পেমেন্ট করুন।</span>
      @endif
    </p>
  </div>
  @if($payStatus !== 'pending')
  <a href="{{ route('event.show') }}" class="btn btn-primary btn-sm" data-en="Pay Now">এখন পেমেন্ট করুন</a>
  @endif
</div>
@endif

</div>
</div>
@endsection
