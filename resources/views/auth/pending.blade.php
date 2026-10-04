@extends('layouts.auth')
@section('title', 'অনুমোদনের অপেক্ষায় — কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২')

@section('content')
<section class="section section-tint">
  <div class="wrap" style="max-width:520px">
    <div class="form-card" style="text-align:center">

      <div style="width:64px;height:64px;border-radius:20px;background:var(--tint);display:grid;place-items:center;margin:0 auto 20px">
        <i data-lucide="clock" width="32" height="32" color="var(--red-700)"></i>
      </div>

      <h2 style="font-size:24px;color:var(--red-900);margin-bottom:10px" data-en="Awaiting Approval">অনুমোদনের অপেক্ষায়</h2>
      <p style="color:var(--muted);margin-bottom:10px;font-size:15px" data-en="Your email has been verified. A reunion committee member will review and approve your account shortly.">
        আপনার ইমেইল যাচাই হয়েছে। পুনর্মিলনী কমিটির একজন সদস্য শীঘ্রই আপনার অ্যাকাউন্ট অনুমোদন করবেন।
      </p>
      <p style="color:var(--muted);font-size:13px" data-en="You will be able to log in once approved. Questions? Contact us on WhatsApp or reach out to the reunion committee.">
        অনুমোদিত হলে আপনি লগইন করতে পারবেন। প্রশ্ন থাকলে WhatsApp বা কমিটির সাথে যোগাযোগ করুন।
      </p>

      <div style="margin-top:28px;display:flex;gap:12px;flex-wrap:wrap;justify-content:center">
        <a href="{{ route('home') }}" class="btn btn-ghost btn-sm" data-en="Back to Home">← হোমে ফিরুন</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
          @csrf
          <button type="submit" class="btn btn-outline btn-sm" data-en="Logout">লগআউট</button>
        </form>
      </div>

    </div>
  </div>
</section>
@endsection
