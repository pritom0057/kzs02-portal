@extends('layouts.app')
@section('title', 'পেমেন্ট ব্যর্থ — KZS 2002')

@section('content')
<div class="m-main">
<div class="wrap" style="max-width:480px;margin-inline:auto;text-align:center;padding-top:48px;padding-bottom:64px">
  <div style="font-size:64px;margin-bottom:16px">{{ $reason === 'cancelled' ? '🚫' : '❌' }}</div>
  <h1 class="m-title" style="color:var(--red-700);margin-bottom:12px">
    @if($reason === 'cancelled')
      <span data-en="Payment Cancelled">পেমেন্ট বাতিল হয়েছে</span>
    @else
      <span data-en="Payment Failed">পেমেন্ট ব্যর্থ হয়েছে</span>
    @endif
  </h1>
  <p style="color:var(--muted);margin-bottom:40px;font-size:15px">
    @if($reason === 'cancelled')
      <span data-en="You cancelled the payment. Your registration is saved — you can try again anytime.">আপনি পেমেন্ট বাতিল করেছেন। আপনার রেজিস্ট্রেশন সংরক্ষিত আছে — যেকোনো সময় আবার চেষ্টা করুন।</span>
    @else
      <span data-en="Something went wrong with your payment. Please try again or contact your bank.">পেমেন্টে সমস্যা হয়েছে। আবার চেষ্টা করুন অথবা ব্যাংকে যোগাযোগ করুন।</span>
    @endif
  </p>
  <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap">
    <a href="{{ route('payment.confirm') }}" class="btn btn-primary" data-en="Try Again">আবার চেষ্টা করুন</a>
    <a href="{{ route('dashboard') }}" class="btn btn-ghost" data-en="Dashboard">ড্যাশবোর্ড</a>
  </div>
</div>
</div>
@endsection
