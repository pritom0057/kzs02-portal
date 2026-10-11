@extends('layouts.auth')
@section('title', 'পাসওয়ার্ড রিসেট — কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২')

@section('content')
<section class="section section-tint">
  <div class="wrap" style="max-width:480px;margin-inline:auto;padding-block:60px">

    <div class="form-card">
      <h3 data-en="Forgot Password">পাসওয়ার্ড ভুলে গেছেন?</h3>
      <p class="sub" data-en="Enter your registered email and we will send you a reset link.">আপনার নিবন্ধিত ইমেইল দিন — আমরা একটি রিসেট লিংক পাঠাব।</p>

      @if(session('success'))
        <div class="alert-ok">✓ {{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert-err">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('password.email') }}" class="fields">
        @csrf

        <div>
          <label for="email" data-en="Email Address">ইমেইল ঠিকানা</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}"
                 required autofocus autocomplete="email"
                 placeholder="your@email.com"
                 class="{{ $errors->has('email') ? 'input-err' : '' }}">
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;border-radius:12px" data-en="Send Reset Link">
          রিসেট লিংক পাঠান
        </button>
      </form>

      <p style="text-align:center;font-size:14px;color:var(--muted);margin-top:18px">
        <a href="{{ route('login') }}" style="color:var(--red-700);font-weight:600;text-decoration:none" data-en="← Back to Login">← লগইনে ফিরুন</a>
      </p>
    </div>

  </div>
</section>
@endsection
