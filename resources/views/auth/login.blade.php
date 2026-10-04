@extends('layouts.auth')
@section('title', 'সদস্য লগইন — কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২')

@section('content')
<section class="section section-tint">
  <div class="wrap reg">

    {{-- Left: description --}}
    <div style="min-width:0">
      <div class="sec-head" style="margin-bottom:0">
        <span class="eyebrow" data-en="Members">সদস্যদের জন্য</span>
        <h2 data-en="Member Login">সদস্য লগইন</h2>
        <p data-en="Sign in to reach the batch wall and your own profile.">ব্যাচ ওয়াল ও নিজের প্রোফাইলে যেতে সাইন ইন করুন।</p>
      </div>
      <ul class="perks">
        <li><span class="tick"><i data-lucide="message-square" width="14" height="14"></i></span><span data-en="Batch Wall: share news and memories, like and comment">ব্যাচ ওয়াল: খবর ও স্মৃতি ভাগ করুন, পছন্দ ও মন্তব্য করুন</span></li>
        <li><span class="tick"><i data-lucide="user-round" width="14" height="14"></i></span><span data-en="Profile: keep your profession, city and a short intro">প্রোফাইল: পেশা, শহর ও ছোট পরিচিতি রাখুন</span></li>
        <li><span class="tick"><i data-lucide="users" width="14" height="14"></i></span><span data-en="Find and open the profiles of your batchmates">ব্যাচের বন্ধুদের প্রোফাইল খুঁজে দেখুন</span></li>
      </ul>
    </div>

    {{-- Right: form card --}}
    <div class="form-card">
      <h3 data-en="Sign In">সাইন ইন করুন</h3>
      <p class="sub" data-en="Enter your credentials to access your account">আপনার অ্যাকাউন্টে প্রবেশ করতে তথ্য দিন</p>

      @if(session('success'))
        <div class="alert-ok">✓ {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert-err">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="fields">
        @csrf

        <div>
          <label for="identifier" data-en="Email / Mobile / Roll number">ইমেইল / মোবাইল / রোল নম্বর</label>
          <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}"
                 required autofocus autocomplete="username"
                 placeholder="your@email.com বা 01XXXXXXXXX"
                 class="{{ $errors->has('identifier') ? 'input-err' : '' }}">
          @error('identifier')
            <p class="field-err">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="password" data-en="Password">পাসওয়ার্ড</label>
          <div style="position:relative">
            <input type="password" id="password" name="password" required
                   autocomplete="current-password"
                   placeholder="আপনার পাসওয়ার্ড"
                   style="padding-right:44px"
                   class="{{ $errors->has('password') ? 'input-err' : '' }}">
            <button type="button" onclick="togglePwd()" title="Show/hide password"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;color:var(--muted);display:flex;align-items:center">
              <i data-lucide="eye" width="18" height="18" id="eye-icon"></i>
            </button>
          </div>
          @error('password')
            <p class="field-err">{{ $message }}</p>
          @enderror
        </div>

        <div style="display:flex;align-items:center;gap:8px">
          <input type="checkbox" id="remember" name="remember"
                 style="width:auto;accent-color:var(--red-700);cursor:pointer">
          <label for="remember" style="margin-bottom:0;font-weight:400;color:var(--muted);cursor:pointer" data-en="Remember me">মনে রাখুন</label>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;border-radius:12px" data-en="Sign In">সাইন ইন করুন</button>
      </form>

      <p style="text-align:center;font-size:14px;color:var(--muted);margin-top:18px">
        <span data-en="New alumni?">নতুন সদস্য?</span>
        <a href="{{ route('register') }}" style="color:var(--red-700);font-weight:600;text-decoration:none" data-en="Create an account">অ্যাকাউন্ট তৈরি করুন</a>
      </p>
    </div>

  </div>
</section>
@endsection

@push('scripts')
<script>
function togglePwd() {
  var inp = document.getElementById('password');
  var ico = document.getElementById('eye-icon');
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.setAttribute('data-lucide', 'eye-off');
  } else {
    inp.type = 'password';
    ico.setAttribute('data-lucide', 'eye');
  }
  if (window.lucide) lucide.createIcons();
}
</script>
@endpush
