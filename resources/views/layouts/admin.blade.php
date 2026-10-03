<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') — KZS 2002</title>
<link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/kzs.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script>
(function(){
  var t='{{ auth()->user()->theme ?? "" }}'||localStorage.getItem('kzs_theme')||'auto';
  localStorage.setItem('kzs_theme',t);
  if(t==='dark') document.documentElement.classList.add('dark');
  else if(t==='light') document.documentElement.classList.remove('dark');
  else if(window.matchMedia('(prefers-color-scheme:dark)').matches) document.documentElement.classList.add('dark');
})();
</script>
<style>
html.dark .adm-topbar { background: var(--surface); border-color: var(--line); }
html.dark .adm-topbar h1 { color: var(--ink); }</style>
<style>
  body { background: var(--bg); }
  .admin-wrap { display: flex; min-height: 100vh; }

  /* Sidebar */
  .adm-side { width: 220px; flex-shrink: 0; background: var(--red-950); color: #dbb8b8; display: flex; flex-direction: column; position: fixed; inset-y: 0; left: 0; z-index: 50; transition: transform .2s; }
  .adm-side-brand { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,.1); display: flex; align-items: center; gap: 10px; }
  .adm-side-brand img { height: 32px; width: auto; }
  .adm-side-label { padding: 6px 16px; background: var(--red-800); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: rgba(255,255,255,.8); }
  .adm-nav { flex: 1; padding: 10px 8px; overflow-y: auto; }
  .adm-nav a { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 10px; font-size: 14px; font-weight: 500; color: rgba(255,255,255,.75); text-decoration: none; transition: background .15s, color .15s; margin-bottom: 2px; }
  .adm-nav a:hover, .adm-nav a.active { background: rgba(255,255,255,.12); color: #fff; }
  .adm-nav .sep { height: 1px; background: rgba(255,255,255,.1); margin: 8px 4px; }
  .adm-footer { padding: 14px 16px; border-top: 1px solid rgba(255,255,255,.1); font-size: 13px; }
  .adm-footer p { color: rgba(255,255,255,.85); font-weight: 600; margin-bottom: 6px; }
  .adm-footer button { background: none; border: 0; color: rgba(255,255,255,.55); cursor: pointer; font-size: 13px; font-family: var(--f-body); }
  .adm-footer button:hover { color: #fff; }
  @media (min-width: 768px) { .adm-side { position: sticky; top: 0; height: 100vh; } }
  @media (max-width: 767px) { .adm-side { transform: translateX(-100%); } .adm-side.open { transform: none; } }

  /* Main area */
  .adm-body { flex: 1; min-width: 0; display: flex; flex-direction: column; margin-left: 220px; }
  @media (max-width: 767px) { .adm-body { margin-left: 0; } }
  .adm-topbar { position: sticky; top: 0; z-index: 40; background: #fff; border-bottom: 1px solid var(--line); padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .adm-topbar h1 { font-size: 17px; font-family: var(--f-display); color: var(--red-900); margin: 0; }
  .adm-main { flex: 1; padding: 24px; }

  /* overlay */
  .adm-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 40; }
  .adm-overlay.show { display: block; }
</style>
@stack('head')
</head>
<body>

<div id="overlay" class="adm-overlay" onclick="toggleSidebar()"></div>

<div class="admin-wrap">

  {{-- SIDEBAR --}}
  <aside class="adm-side" id="sidebar">
    <div class="adm-side-brand">
      <img src="{{ asset('images/kzs02-logo.png') }}" alt="KZS 2002">
    </div>
    <div class="adm-side-label">Admin Panel</div>
    <nav class="adm-nav">
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fa fa-chart-bar fa-fw"></i> Dashboard
      </a>
      <a href="{{ route('admin.alumni.index') }}" class="{{ request()->routeIs('admin.alumni.*') ? 'active' : '' }}">
        <i class="fa fa-users fa-fw"></i> Alumni
      </a>
      <a href="{{ route('admin.registrations.index') }}" class="{{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}">
        <i class="fa fa-ticket fa-fw"></i> Registrations
      </a>
      <div class="sep"></div>
      <a href="{{ route('admin.export.alumni') }}">
        <i class="fa fa-download fa-fw"></i> Export Alumni CSV
      </a>
      <a href="{{ route('admin.export.registrations') }}">
        <i class="fa fa-download fa-fw"></i> Export Reg. CSV
      </a>
      <div class="sep"></div>
      <a href="{{ route('dashboard') }}" style="color:rgba(255,255,255,.5)">
        <i class="fa fa-arrow-left fa-fw"></i> Member Portal
      </a>
    </nav>
    <div class="adm-footer">
      <p>{{ auth()->user()->name }}</p>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
      </form>
    </div>
  </aside>

  {{-- MAIN CONTENT --}}
  <div class="adm-body">
    <div class="adm-topbar">
      <div style="display:flex;align-items:center;gap:10px">
        <button class="icon-btn" onclick="toggleSidebar()" aria-label="Toggle sidebar" style="display:none" id="menuBtn">
          <i class="fa fa-bars"></i>
        </button>
        <h1>@yield('heading', 'Dashboard')</h1>
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <div class="lang" role="group" aria-label="Theme">
          <button type="button" class="lang-btn" id="themeBtnAuto"  onclick="setTheme('auto')"  aria-pressed="true">🔆</button>
          <button type="button" class="lang-btn" id="themeBtnLight" onclick="setTheme('light')" aria-pressed="false">☀️</button>
          <button type="button" class="lang-btn" id="themeBtnDark"  onclick="setTheme('dark')"  aria-pressed="false">🌙</button>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">View Portal →</a>
      </div>
    </div>

    <main class="adm-main">
      @if(session('success'))
        <div class="alert-ok">✓ {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert-err">{{ session('error') }}</div>
      @endif
      @yield('content')
    </main>
  </div>

</div>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('show');
}
if (window.innerWidth < 768) {
  var btn = document.getElementById('menuBtn');
  if (btn) btn.style.display = '';
}
function _getTheme(){return localStorage.getItem('kzs_theme')||'auto';}
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
  var csrf=document.querySelector('meta[name="csrf-token"]');
  if(csrf)fetch('/settings/theme',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf.content},body:JSON.stringify({theme:t})}).catch(function(){});
}
(function(){_applyTheme(_getTheme());})();
document.addEventListener('DOMContentLoaded',function(){_applyTheme(_getTheme());});
</script>
@stack('scripts')
</body>
</html>
