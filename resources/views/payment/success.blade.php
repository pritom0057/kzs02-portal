@extends('layouts.app')
@section('title', 'পেমেন্ট সফল — KZS 2002')

@section('content')
<div class="m-main">
<div class="wrap" style="max-width:480px;margin-inline:auto;text-align:center;padding-top:48px;padding-bottom:64px">
  <div style="font-size:64px;margin-bottom:16px">✅</div>
  <h1 class="m-title" style="color:#166534;margin-bottom:12px" data-en="Payment Successful!">পেমেন্ট সফল হয়েছে!</h1>
  <p style="color:var(--muted);margin-bottom:8px;font-size:16px">
    ধন্যবাদ, <strong>{{ $alumni->name }}</strong>। আপনার রেজিস্ট্রেশন নিশ্চিত হয়েছে।
  </p>
  <p style="color:var(--muted);font-size:14px;margin-bottom:40px" data-en="You will receive a confirmation email. See you at the reunion!">
    একটি নিশ্চিতকরণ ইমেইল পাঠানো হবে। রিইউনিয়নে দেখা হবে!
  </p>
  <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap">
    <a href="{{ route('dashboard') }}" class="btn btn-primary" data-en="Go to Dashboard">ড্যাশবোর্ডে যান</a>
    <a href="{{ route('directory') }}" class="btn btn-ghost" data-en="View Directory">সদস্য তালিকা</a>
  </div>
</div>
</div>
@endsection
