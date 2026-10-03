<!doctype html>
<html lang="bn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'KZS 2002 — সদস্য পোর্টাল')</title>
<link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/kzs.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/lucide@0.383.0/dist/umd/lucide.min.js" defer></script>
<script src="{{ asset('js/kzs-lang.js') }}" defer></script>
@stack('head')
<script>
(function(){
  var t='{{ auth()->check() && auth()->user()->theme ? auth()->user()->theme : "" }}'||localStorage.getItem('kzs_theme')||'auto';
  localStorage.setItem('kzs_theme',t);
  if(t==='dark') document.documentElement.classList.add('dark');
  else if(t==='light') document.documentElement.classList.remove('dark');
  else if(window.matchMedia('(prefers-color-scheme:dark)').matches) document.documentElement.classList.add('dark');
  var l='{{ auth()->check() && auth()->user()->lang ? auth()->user()->lang : "" }}'||localStorage.getItem('kzs_lang')||'bn';
  localStorage.setItem('kzs_lang', l);
})();
</script>
</head>
<body>

{{-- ── MEMBER HEADER ── --}}
<header class="site-header">
  <div class="wrap hdr">

    {{-- Brand --}}
    <a href="{{ route('home') }}" class="brand" aria-label="KZS02">
      <img src="{{ asset('images/kzs02-logo.png') }}" alt="KZS 2002 লোগো" height="40">
      <span class="brand-text">
        <strong data-en="Batch-2002 Members">ব্যাচ-২০০২ সদস্য এলাকা</strong>
        @auth<small>{{ auth()->user()->name }}</small>@endauth
      </span>
    </a>

    {{-- Lang toggle --}}
    <div class="lang" role="group" aria-label="Language / ভাষা">
      <button type="button" class="lang-btn" data-lang="bn" aria-pressed="true">বাংলা</button>
      <button type="button" class="lang-btn" data-lang="en" aria-pressed="false">EN</button>
    </div>

    {{-- Dark mode toggle --}}
    <div class="lang" role="group" aria-label="Theme">
      <button type="button" class="lang-btn" id="themeBtnAuto"  onclick="setTheme('auto')"  aria-pressed="true">🔆</button>
      <button type="button" class="lang-btn" id="themeBtnLight" onclick="setTheme('light')" aria-pressed="false">☀️</button>
      <button type="button" class="lang-btn" id="themeBtnDark"  onclick="setTheme('dark')"  aria-pressed="false">🌙</button>
    </div>

    @auth
    {{-- Notification bell --}}
    @php $unreadNotifCount = \App\Models\WallNotification::where('recipient_id', auth()->id())->whereNull('read_at')->count(); @endphp
    <div class="notif-wrap" id="notifBell">
      <button class="icon-btn" onclick="toggleNotifDropdown()" id="notifBtn" title="Notifications" aria-label="Notifications">
        <i class="fa fa-bell" style="font-size:16px"></i>
        <span id="notifBadge" class="notif-badge {{ $unreadNotifCount > 0 ? '' : 'hidden' }}">{{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}</span>
      </button>
      <div id="notifDropdown" class="notif-drop hidden">
        <div class="notif-head">
          <span data-en="Notifications">নোটিফিকেশন</span>
          <button onclick="markAllNotifRead()" data-en="Mark all read">সব পড়া হিসেবে চিহ্নিত করুন</button>
        </div>
        <div id="notifList" class="notif-list">
          <div style="padding:28px 16px;text-align:center;font-size:14px;color:var(--muted)" data-en="Loading…">লোড হচ্ছে…</div>
        </div>
        <div class="notif-foot">
          <a href="{{ route('wall.index') }}" data-en="Go to Wall">ওয়ালে যান</a>
        </div>
      </div>
    </div>

    {{-- Admin panel --}}
    @if(auth()->user()->isAdmin())
      <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm" data-en="Admin">অ্যাডমিন</a>
    @endif
    @endauth

    {{-- Mobile menu toggle --}}
    <button class="icon-btn mob-only" onclick="toggleMobileMenu()" aria-label="Menu" id="menuBtn">
      <i data-lucide="menu" width="20" height="20"></i>
    </button>

    {{-- Desktop nav --}}
    <nav class="nav" aria-label="Members" id="mainNav">
      <a href="{{ route('wall.index') }}" class="{{ request()->routeIs('wall.*') ? 'hl' : '' }}" data-en="Wall">ওয়াল</a>
      <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'hl' : '' }}" data-en="Dashboard">ড্যাশবোর্ড</a>
      <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'hl' : '' }}" data-en="My Profile">আমার প্রোফাইল</a>
      <a href="{{ route('directory') }}" class="{{ request()->routeIs('directory') ? 'hl' : '' }}" data-en="Directory">সদস্য তালিকা</a>
      <a href="{{ route('event.show') }}" class="{{ request()->routeIs('event.*') ? 'hl' : '' }}" data-en="Registration">ইভেন্ট</a>
      <a href="{{ route('home') }}" data-en="Public Site">মূল সাইট</a>
      @auth
      <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button type="submit" style="background:none;border:0;cursor:pointer;padding:6px 12px;border-radius:999px;font:500 15px/1 var(--f-body);color:var(--muted);vertical-align:middle" data-en="Logout">লগআউট</button>
      </form>
      @endauth
    </nav>

  </div>
</header>

<main>
  <div class="wrap" style="padding-top:8px;padding-bottom:4px">
    @if(session('success'))
      <div class="alert-ok">✓ {{ session('success') }}</div>
    @endif
    @if(session('info'))
      <div class="alert-inf">{{ session('info') }}</div>
    @endif
    @if(session('error'))
      <div class="alert-err">{{ session('error') }}</div>
    @endif
  </div>

  @yield('content')
</main>

<footer class="site-footer" style="padding-block:20px">
  <div class="wrap">
    <p class="copy" style="border:0;padding:0" data-en="&copy; {{ date('Y') }} Kushtia Zilla School Batch-2002. All rights reserved.">&copy; {{ date('Y') }} কুষ্টিয়া জিলা স্কুল ব্যাচ-২০০২। সর্বস্বত্ব সংরক্ষিত।</p>
  </div>
</footer>

<script>
/* lucide icons */
document.addEventListener('DOMContentLoaded', function(){ if(window.lucide) lucide.createIcons(); });

/* mobile menu */
function toggleMobileMenu() {
  var nav = document.getElementById('mainNav');
  if (nav) nav.classList.toggle('mob-open');
}
document.addEventListener('click', function(e) {
  var nav = document.getElementById('mainNav');
  var btn = document.getElementById('menuBtn');
  if (nav && btn && !nav.contains(e.target) && !btn.contains(e.target)) {
    nav.classList.remove('mob-open');
  }
});

/* notification dropdown */
function toggleNotifDropdown() {
  var dd = document.getElementById('notifDropdown');
  if (!dd) return;
  if (dd.classList.contains('hidden')) {
    dd.classList.remove('hidden');
    loadNotifications();
  } else {
    dd.classList.add('hidden');
  }
}

async function loadNotifications() {
  var list = document.getElementById('notifList');
  if (!list) return;
  try {
    var csrf = document.querySelector('meta[name="csrf-token"]');
    var res = await fetch('/wall/notifications', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf ? csrf.content : '' } });
    var notifs = await res.json();
    var badge = document.getElementById('notifBadge');
    if (badge) badge.classList.add('hidden');
    if (!notifs.length) {
      list.innerHTML = '<div style="padding:28px 16px;text-align:center;font-size:14px;color:var(--muted)">কোনো নোটিফিকেশন নেই</div>';
      return;
    }
    list.innerHTML = notifs.map(function (n) {
      var av = n.photo
        ? '<img src="' + n.photo + '" class="avatar sm" alt="" style="flex-shrink:0">'
        : '<div class="avatar sm" style="background:var(--red-700);color:#fff;display:grid;place-items:center;font-weight:700;font-size:13px;flex-shrink:0">' + n.initial + '</div>';
      var dot = !n.read ? '<div class="notif-dot"></div>' : '';
      return '<a href="' + (n.url || '/wall') + '" class="notif-item ' + (!n.read ? 'unread' : '') + '">'
        + av
        + '<div style="flex:1;min-width:0"><p style="font-size:14px;color:var(--ink);line-height:1.4;margin:0">' + n.message + '</p>'
        + '<p style="font-size:12px;color:var(--muted);margin:2px 0 0">' + n.time + '</p></div>'
        + dot + '</a>';
    }).join('');
  } catch (e) {
    if (list) list.innerHTML = '<div style="padding:16px;text-align:center;font-size:14px;color:var(--red-700)">লোড ব্যর্থ হয়েছে</div>';
  }
}

async function markAllNotifRead() {
  var csrf = document.querySelector('meta[name="csrf-token"]');
  try { await fetch('/wall/notifications/read', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf ? csrf.content : '' } }); } catch (e) {}
  var badge = document.getElementById('notifBadge');
  if (badge) badge.classList.add('hidden');
  document.querySelectorAll('.notif-item.unread').forEach(function (el) { el.classList.remove('unread'); });
  document.querySelectorAll('.notif-dot').forEach(function (el) { el.remove(); });
}

function updateNotifBadge(count) {
  var badge = document.getElementById('notifBadge');
  if (!badge) return;
  if (count > 0) { badge.textContent = count > 9 ? '9+' : count; badge.classList.remove('hidden'); }
  else { badge.classList.add('hidden'); }
}

/* close notif dropdown on outside click */
document.addEventListener('click', function (e) {
  var bell = document.getElementById('notifBell');
  if (bell && !bell.contains(e.target)) {
    var dd = document.getElementById('notifDropdown');
    if (dd) dd.classList.add('hidden');
  }
});

/* theme */
function _getTheme(){return localStorage.getItem('kzs_theme')||'auto';}
function _applyTheme(t){
  var isDark = t==='dark' || (t==='auto' && window.matchMedia('(prefers-color-scheme:dark)').matches);
  isDark ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
  var ba=document.getElementById('themeBtnAuto'), bl=document.getElementById('themeBtnLight'), bd=document.getElementById('themeBtnDark');
  if(ba) ba.setAttribute('aria-pressed', t==='auto'  ? 'true' : 'false');
  if(bl) bl.setAttribute('aria-pressed', t==='light' ? 'true' : 'false');
  if(bd) bd.setAttribute('aria-pressed', t==='dark'  ? 'true' : 'false');
}
function setTheme(t){
  localStorage.setItem('kzs_theme',t);
  _applyTheme(t);
  var csrf=document.querySelector('meta[name="csrf-token"]');
  if(csrf) fetch('/settings/theme',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf.content},body:JSON.stringify({theme:t})}).catch(function(){});
}
(function(){_applyTheme(_getTheme());})();
document.addEventListener('DOMContentLoaded', function(){_applyTheme(_getTheme());});
</script>

@stack('scripts')
</body>
</html>
