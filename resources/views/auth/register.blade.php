@extends('layouts.auth')
@section('title', 'সদস্য রেজিস্ট্রেশন — কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২')

@section('content')
<section class="section section-tint">
  <div class="wrap reg">

    {{-- Left: description --}}
    <div style="min-width:0">
      <div class="sec-head" style="margin-bottom:0">
        <span class="eyebrow" data-en="Join Us">সদস্য হোন</span>
        <h2 data-en="Come back to your batch">আপনার ব্যাচের সঙ্গে আবার যুক্ত হোন</h2>
      </div>
      <ul class="perks">
        <li><span class="tick"><i data-lucide="check" width="14" height="14"></i></span><span data-en="Get news of the reunion and events first-hand">রিইউনিয়ন ও ইভেন্টের খবর সরাসরি পাবেন</span></li>
        <li><span class="tick"><i data-lucide="check" width="14" height="14"></i></span><span data-en="Reconnect with your batchmates">ব্যাচের বন্ধুদের সঙ্গে আবার যোগাযোগ হবে</span></li>
        <li><span class="tick"><i data-lucide="check" width="14" height="14"></i></span><span data-en="Take part in charity and social service">দাতব্য ও সমাজসেবা কার্যক্রমে অংশ নিতে পারবেন</span></li>
      </ul>
    </div>

    {{-- Right: form card --}}
    <div class="form-card">
      <h3 data-en="Member Registration Form">সদস্য হিসেবে রেজিস্ট্রেশন ফরম</h3>
      <p class="sub" data-en="Fill in the details below to join our organization's activities">আমাদের সংগঠনের কার্যক্রমে যুক্ত হতে নিচের তথ্যগুলো পূরণ করুন</p>

      @if(session('success'))
        <div class="alert-ok">✓ {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert-err">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('register') }}" class="fields">
        @csrf

        {{-- Full name --}}
        <div>
          <label for="name" data-en="Full name (Bengali or English)">পূর্ণ নাম (বাংলা বা ইংরেজি) <span style="color:var(--red-600)">*</span></label>
          <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                 placeholder="আপনার নাম লিখুন"
                 data-en-ph="Enter your name"
                 class="{{ $errors->has('name') ? 'input-err' : '' }}">
          @error('name')<p class="field-err">{{ $message }}</p>@enderror
        </div>

        {{-- Email --}}
        <div>
          <label for="email" data-en="Email address">ইমেইল ঠিকানা <span style="color:var(--red-600)">*</span></label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required
                 placeholder="your@email.com"
                 class="{{ $errors->has('email') ? 'input-err' : '' }}">
          @error('email')<p class="field-err">{{ $message }}</p>@enderror
        </div>

        {{-- Mobile + Roll --}}
        <div class="two">
          <div style="min-width:0">
            <label for="mobile" data-en="Mobile number (WhatsApp)">মোবাইল নম্বর (WhatsApp)</label>
            <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}"
                   placeholder="01XXXXXXXXX">
          </div>
          <div style="min-width:0">
            <label for="roll_number" data-en="Roll / Section (school days)">রোল / সেকশন (স্কুল জীবন)</label>
            <input type="text" id="roll_number" name="roll_number" value="{{ old('roll_number') }}"
                   placeholder="যেমন: সেকশন এ, রোল ১২"
                   data-en-ph="e.g. Section A, Roll 12"
                   class="{{ $errors->has('roll_number') ? 'input-err' : '' }}">
            @error('roll_number')<p class="field-err">{{ $message }}</p>@enderror
          </div>
        </div>

        {{-- Password --}}
        <div>
          <label for="reg_password" data-en="Password">পাসওয়ার্ড <span style="color:var(--red-600)">*</span></label>
          <div style="position:relative">
            <input type="password" id="reg_password" name="password" required
                   placeholder="কমপক্ষে ৮ অক্ষর" data-en-ph="Min. 8 characters"
                   style="padding-right:44px"
                   class="{{ $errors->has('password') ? 'input-err' : '' }}">
            <button type="button" onclick="togglePwd('reg_password','eye1')"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;color:var(--muted);display:flex;align-items:center">
              <i data-lucide="eye" width="18" height="18" id="eye1"></i>
            </button>
          </div>
          @error('password')<p class="field-err">{{ $message }}</p>@enderror
        </div>

        {{-- Confirm password --}}
        <div>
          <label for="reg_confirm" data-en="Repeat password">পাসওয়ার্ড পুনরায় <span style="color:var(--red-600)">*</span></label>
          <div style="position:relative">
            <input type="password" id="reg_confirm" name="password_confirmation" required
                   placeholder="পাসওয়ার্ড আবার লিখুন" data-en-ph="Repeat your password"
                   style="padding-right:44px">
            <button type="button" onclick="togglePwd('reg_confirm','eye2')"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;color:var(--muted);display:flex;align-items:center">
              <i data-lucide="eye" width="18" height="18" id="eye2"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;border-radius:12px" data-en="Create Account">অ্যাকাউন্ট তৈরি করুন</button>
      </form>

      <p style="text-align:center;font-size:14px;color:var(--muted);margin-top:18px">
        <span data-en="Already registered?">আগেই রেজিস্ট্রেশন করেছেন?</span>
        <a href="{{ route('login') }}" style="color:var(--red-700);font-weight:600;text-decoration:none" data-en="Sign in">সাইন ইন করুন</a>
      </p>
    </div>

  </div>
</section>
@endsection

@push('scripts')
<script>
function togglePwd(id, iconId) {
  var inp = document.getElementById(id);
  var ico = document.getElementById(iconId);
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
