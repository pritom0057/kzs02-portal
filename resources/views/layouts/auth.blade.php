<!doctype html>
<html lang="bn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২')</title>
<link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/kzs.css') }}">
<script src="https://cdn.jsdelivr.net/npm/lucide@0.383.0/dist/umd/lucide.min.js" defer></script>
<script src="{{ asset('js/kzs-lang.js') }}" defer></script>
<script>
(function(){
  var t=localStorage.getItem('kzs_theme')||'light';
  if(t==='dark') document.documentElement.classList.add('dark');
  else document.documentElement.classList.remove('dark');
})();
</script>
</head>
<body>

{{-- ── HEADER ── --}}
<header class="site-header">
  <div class="wrap hdr">
    <a href="{{ route('home') }}" class="brand" aria-label="KZS02">
      <img src="{{ asset('images/kzs02-logo.png') }}" alt="KZS 2002 লোগো" height="40">
      <span class="brand-text">
        <strong data-en="Kushtia Zilla School Batch-2002">কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২</strong>
      </span>
    </a>
    <div class="lang" role="group" aria-label="Language / ভাষা">
      <button type="button" class="lang-btn" data-lang="bn" aria-pressed="true">বাংলা</button>
      <button type="button" class="lang-btn" data-lang="en" aria-pressed="false">EN</button>
    </div>
    <div class="lang" role="group" aria-label="Theme">
      <button type="button" class="lang-btn" id="themeBtnAuto"  onclick="setTheme('auto')"  aria-pressed="true">🔆</button>
      <button type="button" class="lang-btn" id="themeBtnLight" onclick="setTheme('light')" aria-pressed="false">☀️</button>
      <button type="button" class="lang-btn" id="themeBtnDark"  onclick="setTheme('dark')"  aria-pressed="false">🌙</button>
    </div>
    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm"><i data-lucide="lock" width="15" height="15"></i><span data-en="Member Login">সদস্য লগইন</span></a>
    <a href="{{ route('register') }}" class="btn btn-primary btn-sm" data-en="Join Us">সদস্য হোন</a>
    <button class="icon-btn" id="menuBtn" onclick="toggleMobileMenu()" aria-label="Menu">
      <i data-lucide="menu" width="20" height="20"></i>
    </button>
    <nav class="nav" id="mainNav" aria-label="Main">
      <a href="{{ route('home') }}#home" data-en="Home">হোম</a>
      <a href="{{ route('home') }}#about" data-en="About Us">আমাদের সম্পর্কে</a>
      <a href="{{ route('home') }}#activities" data-en="Activities">কার্যক্রম</a>
      <a href="{{ route('home') }}#gallery" data-en="Gallery">ছবিঘর</a>
      <a href="{{ route('home') }}#organogram" data-en="Organogram">অর্গানোগ্রাম</a>
      <a href="{{ route('home') }}#reunion" data-en="Reunion">রিইউনিয়ন</a>
      <a href="{{ route('home') }}#contact" data-en="Contact">যোগাযোগ</a>
    </nav>
  </div>
</header>

<main>
@yield('content')
</main>

{{-- ── FOOTER ── --}}
<footer id="contact" class="site-footer">
  <div class="wrap">
    <div class="foot">
      <div>
        <span class="foot-logo"><img src="{{ asset('images/kzs02-logo.png') }}" alt="KZS 2002" height="34"></span>
        <p class="foot-slogan" data-en="Unity, Friendship and Progress">ঐক্য, বন্ধুত্ব ও প্রগতি</p>
        <p data-en="A non-political social welfare organization of the former students of the Kushtia Zilla School SSC 2002 batch.">কুষ্টিয়া জিলা স্কুল এসএসসি ২০০২ ব্যাচের প্রাক্তন ছাত্রদের একটি অরাজনৈতিক ও সামাজিক কল্যাণমূলক সংগঠন।</p>
      </div>
      <div>
        <h4 data-en="Quick Links">দ্রুত লিংক</h4>
        <ul>
          <li><a href="{{ route('home') }}#about" data-en="About Us">আমাদের সম্পর্কে</a></li>
          <li><a href="{{ route('home') }}#activities" data-en="Activities">কার্যক্রম</a></li>
          <li><a href="{{ route('home') }}#gallery" data-en="Gallery">ছবিঘর</a></li>
          <li><a href="{{ route('home') }}#organogram" data-en="Organogram">অর্গানোগ্রাম</a></li>
          <li><a href="{{ route('home') }}#reunion" data-en="Reunion">রিইউনিয়ন</a></li>
          <li><a href="{{ route('register') }}" data-en="Member Registration">সদস্য রেজিস্ট্রেশন</a></li>
          <li><a href="{{ route('login') }}" data-en="Member Login">সদস্য লগইন</a></li>
        </ul>
      </div>
      <div>
        <h4 data-en="Contact">যোগাযোগ</h4>
        <p class="cline"><i data-lucide="map-pin" width="16" height="16"></i><span data-en="Kushtia Zilla School, Kushtia, Bangladesh">কুষ্টিয়া জিলা স্কুল, কুষ্টিয়া, বাংলাদেশ</span></p>
        <p class="cline"><i data-lucide="mail" width="16" height="16"></i><span>admin@kzs02.com</span></p>
        <p class="cline"><i data-lucide="phone" width="16" height="16"></i><span>+880 1717-058286</span></p>
      </div>
    </div>
    <p class="copy" data-en="&copy; {{ date('Y') }} Kushtia Zilla School Batch-2002. All rights reserved.">&copy; {{ date('Y') }} কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২। সর্বস্বত্ব সংরক্ষিত।</p>
  </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.lucide) lucide.createIcons();
});
function toggleMobileMenu(){var n=document.getElementById('mainNav');if(n)n.classList.toggle('mob-open');}
document.addEventListener('click',function(e){var n=document.getElementById('mainNav'),b=document.getElementById('menuBtn');if(n&&b&&!n.contains(e.target)&&!b.contains(e.target))n.classList.remove('mob-open');});
function _getTheme(){return localStorage.getItem('kzs_theme')||'light';}
function _applyTheme(t){
  var isDark=t==='dark'||(t==='auto'&&window.matchMedia('(prefers-color-scheme:dark)').matches);
  isDark?document.documentElement.classList.add('dark'):document.documentElement.classList.remove('dark');
  var ba=document.getElementById('themeBtnAuto'),bl=document.getElementById('themeBtnLight'),bd=document.getElementById('themeBtnDark');
  if(ba)ba.setAttribute('aria-pressed',t==='auto'?'true':'false');
  if(bl)bl.setAttribute('aria-pressed',t==='light'?'true':'false');
  if(bd)bd.setAttribute('aria-pressed',t==='dark'?'true':'false');
}
function setTheme(t){
  localStorage.setItem('kzs_theme',t);
  _applyTheme(t);
}
(function(){_applyTheme(_getTheme());})();
document.addEventListener('DOMContentLoaded',function(){_applyTheme(_getTheme());});
</script>
@stack('scripts')
</body>
</html>
