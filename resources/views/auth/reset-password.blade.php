@extends('layouts.auth')
@section('title', 'নতুন পাসওয়ার্ড সেট করুন — কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২')

@section('content')
<section class="section section-tint">
  <div class="wrap" style="max-width:480px;margin-inline:auto;padding-block:60px">

    <div class="form-card">
      <h3 data-en="Set New Password">নতুন পাসওয়ার্ড সেট করুন</h3>
      <p class="sub" data-en="Choose a new password for your account.">আপনার অ্যাকাউন্টের জন্য একটি নতুন পাসওয়ার্ড বেছে নিন।</p>

      @if($errors->any())
        <div class="alert-err">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('password.update') }}" class="fields">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
          <label for="email" data-en="Email Address">ইমেইল ঠিকানা</label>
          <input type="email" id="email" name="email" value="{{ old('email', $email) }}"
                 required autofocus autocomplete="email"
                 placeholder="your@email.com"
                 class="{{ $errors->has('email') ? 'input-err' : '' }}">
        </div>

        <div>
          <label for="password" data-en="New Password">নতুন পাসওয়ার্ড</label>
          <div style="position:relative">
            <input type="password" id="password" name="password" required
                   autocomplete="new-password"
                   placeholder="কমপক্ষে ৬ অক্ষর"
                   data-en-ph="At least 6 characters"
                   style="padding-right:44px"
                   class="{{ $errors->has('password') ? 'input-err' : '' }}">
            <button type="button" onclick="togglePwd('password','eye1')" title="Show/hide"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;color:var(--muted);display:flex;align-items:center">
              <i data-lucide="eye" width="18" height="18" id="eye1"></i>
            </button>
          </div>
        </div>

        <div>
          <label for="password_confirmation" data-en="Confirm New Password">পাসওয়ার্ড নিশ্চিত করুন</label>
          <div style="position:relative">
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   autocomplete="new-password"
                   placeholder="পাসওয়ার্ড আবার দিন"
                   data-en-ph="Re-enter your password"
                   style="padding-right:44px"
                   class="{{ $errors->has('password_confirmation') ? 'input-err' : '' }}">
            <button type="button" onclick="togglePwd('password_confirmation','eye2')" title="Show/hide"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:0;cursor:pointer;color:var(--muted);display:flex;align-items:center">
              <i data-lucide="eye" width="18" height="18" id="eye2"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;border-radius:12px" data-en="Reset Password">
          পাসওয়ার্ড পরিবর্তন করুন
        </button>
      </form>

      <p style="text-align:center;font-size:14px;color:var(--muted);margin-top:18px">
        <a href="{{ route('login') }}" style="color:var(--red-700);font-weight:600;text-decoration:none" data-en="← Back to Login">← লগইনে ফিরুন</a>
      </p>
    </div>

  </div>
</section>
@endsection

@push('scripts')
<script>
function togglePwd(fieldId, iconId) {
  var inp = document.getElementById(fieldId);
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
