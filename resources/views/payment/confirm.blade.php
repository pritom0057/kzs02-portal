@extends('layouts.app')
@section('title', 'পেমেন্ট নিশ্চিত করুন — KZS 2002')

@section('content')
<div class="m-main">
<div class="wrap" style="max-width:480px;margin-inline:auto">

  <h1 class="m-title" style="font-size:22px;margin-bottom:24px" data-en="Confirm Payment">পেমেন্ট নিশ্চিত করুন</h1>

  <div class="panel" style="margin-bottom:16px">
    <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:16px" data-en="Order Summary">অর্ডার সারসংক্ষেপ</p>

    <dl style="display:grid;gap:12px;font-size:14px">
      <div style="display:flex;justify-content:space-between">
        <dt style="color:var(--muted)" data-en="Alumnus">আলামনাই</dt>
        <dd style="font-weight:600;color:var(--ink)">{{ $alumni->name }}</dd>
      </div>
      <div style="display:flex;justify-content:space-between">
        <dt style="color:var(--muted)" data-en="Roll Number">রোল নম্বর</dt>
        <dd style="color:var(--ink)">{{ $alumni->roll_number }}</dd>
      </div>
      <div style="display:flex;justify-content:space-between">
        <dt style="color:var(--muted)" data-en="T-Shirt Size">টি-শার্ট সাইজ</dt>
        <dd style="color:var(--ink)">{{ $registration->tshirt_size }}</dd>
      </div>
      <div style="display:flex;justify-content:space-between">
        <dt style="color:var(--muted)" data-en="Additional Guests">অতিরিক্ত গেস্ট</dt>
        <dd style="color:var(--ink)">{{ $registration->guest_count }}</dd>
      </div>
      <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid var(--line)">
        <dt style="font-weight:600;color:var(--ink)" data-en="Registration Fee">রেজিস্ট্রেশন ফি</dt>
        <dd style="font-size:20px;font-weight:700;color:var(--red-700)">৳ {{ number_format($fee, 0) }}</dd>
      </div>
    </dl>
  </div>

  <div class="alert-inf" style="margin-bottom:16px">
    <span data-en="You will be redirected to SSLCommerz to pay securely via bKash, Nagad, card, or internet banking.">আপনাকে SSLCommerz-এ নিয়ে যাওয়া হবে যেখানে bKash, Nagad, কার্ড বা ইন্টারনেট ব্যাংকিং দিয়ে পেমেন্ট করতে পারবেন।</span>
  </div>

  <form method="POST" action="{{ route('payment.initiate') }}">
    @csrf
    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:16px;padding:14px">
      <span data-en="Pay">পেমেন্ট করুন</span> ৳ {{ number_format($fee, 0) }}
    </button>
  </form>

  <a href="{{ route('dashboard') }}" style="display:block;text-align:center;font-size:14px;color:var(--muted);margin-top:16px;text-decoration:none" data-en="Pay later">পরে পেমেন্ট করুন</a>
</div>
</div>
@endsection
