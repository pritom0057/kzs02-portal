@extends('layouts.auth')
@section('title', 'ইমেইল যাচাই — কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২')

@section('content')
<section class="section section-tint">
  <div class="wrap" style="max-width:520px">
    <div class="form-card" style="text-align:center">

      <div style="width:64px;height:64px;border-radius:20px;background:var(--tint);display:grid;place-items:center;margin:0 auto 20px">
        <i data-lucide="mail" width="32" height="32" color="var(--red-700)"></i>
      </div>

      <h2 style="font-size:24px;color:var(--red-900);margin-bottom:8px" data-en="Check Your Email">আপনার ইমেইল চেক করুন</h2>
      <p style="color:var(--muted);margin-bottom:24px;font-size:15px" data-en="We sent a 6-digit code to your email. Enter it below to verify your account.">
        আমরা আপনার ইমেইলে একটি ৬ সংখ্যার কোড পাঠিয়েছি।
        @if(session('otp_email'))
          <br><strong style="color:var(--red-900)">{{ session('otp_email') }}</strong>
        @endif
      </p>

      @if(session('success'))
        <div class="alert-ok" style="text-align:left">✓ {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert-err" style="text-align:left">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('otp.verify') }}" class="fields">
        @csrf
        <div>
          <input type="text" name="otp" maxlength="6" inputmode="numeric" autofocus
                 placeholder="000000"
                 style="text-align:center;font-size:28px;font-weight:700;letter-spacing:0.3em;font-family:var(--f-display)"
                 class="{{ $errors->has('otp') ? 'input-err' : '' }}">
          @error('otp')<p class="field-err" style="text-align:left">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;border-radius:12px" data-en="Verify Code">কোড যাচাই করুন</button>
      </form>

      <form method="POST" action="{{ route('otp.resend') }}" style="margin-top:16px">
        @csrf
        <button type="submit" style="background:none;border:0;color:var(--red-700);font-size:14px;font-weight:600;cursor:pointer;font-family:var(--f-body)" data-en="Didn't receive the code? Resend">
          কোড পাননি? পুনরায় পাঠান
        </button>
      </form>

    </div>
  </div>
</section>
@endsection
