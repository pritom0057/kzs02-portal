<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — KZS 2002</title>
    <script>
    (function(){
        var t = localStorage.getItem('theme');
        if(t==='dark'||(t!=='light'&&window.matchMedia('(prefers-color-scheme:dark)').matches)){
            document.documentElement.classList.add('dark');
        }
    })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#c0392b',
                        brand:   '#c0392b',
                        kgreen:  '#27ae60',
                        kgray:   '#555555',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex">

    {{-- Mobile overlay backdrop --}}
    <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="w-56 bg-kgray dark:bg-gray-900 text-white flex-shrink-0 min-h-screen flex flex-col fixed lg:static inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200">
        <div class="px-4 py-3 bg-white dark:bg-gray-800 border-b-4 border-brand">
            <img src="/images/logo.jpg" alt="KZS 2002" class="h-9 w-auto">
        </div>
        <div class="px-4 py-1.5 bg-kgreen text-white text-xs font-semibold uppercase tracking-widest">
            Admin Panel
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 font-semibold' : '' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('admin.alumni.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.alumni.*') ? 'bg-white/10 font-semibold' : '' }}">
                👥 Alumni
            </a>
            <a href="{{ route('admin.registrations.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('admin.registrations.*') ? 'bg-white/10 font-semibold' : '' }}">
                🎟️ Registrations
            </a>
            <div class="border-t border-white/10 my-2"></div>
            <a href="{{ route('admin.export.alumni') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 transition text-white/80">
                📥 Export Alumni CSV
            </a>
            <a href="{{ route('admin.export.registrations') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 transition text-white/80">
                📥 Export Reg. CSV
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-white/10 text-xs text-white/60">
            <p class="font-medium text-white/80">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button class="hover:text-white transition">Logout</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <button onclick="toggleSidebar()" class="lg:hidden mr-3 text-gray-500 dark:text-gray-400 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">☰</button>
                <h1 class="text-base font-semibold text-kgray dark:text-gray-200">@yield('heading', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-2">
                <button id="themeBtn" onclick="cycleTheme()" title="Toggle theme"
                    class="text-lg w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    ⚙️
                </button>
                <a href="{{ route('dashboard') }}" class="text-xs text-gray-400 dark:text-gray-500 hover:text-kgreen transition">View Portal &rarr;</a>
            </div>
        </header>
        <main class="flex-1 px-6 py-6 dark:bg-gray-900">
            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-300 rounded px-4 py-3 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-300 rounded px-4 py-3 text-sm">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.toggle('hidden');
}
function _getTheme(){return localStorage.getItem('theme')||'auto';}
function _applyTheme(t){
    if(t==='dark') document.documentElement.classList.add('dark');
    else if(t==='light') document.documentElement.classList.remove('dark');
    else { window.matchMedia('(prefers-color-scheme:dark)').matches ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark'); }
}
function cycleTheme(){
    var order=['light','dark','auto'], icons={light:'☀️',dark:'🌙',auto:'⚙️'};
    var next=order[(order.indexOf(_getTheme())+1)%3];
    next==='auto'?localStorage.removeItem('theme'):localStorage.setItem('theme',next);
    _applyTheme(next);
    var el=document.getElementById('themeBtn');
    if(el) el.textContent=icons[next];
}
(function(){
    var icons={light:'☀️',dark:'🌙',auto:'⚙️'};
    var el=document.getElementById('themeBtn');
    if(el) el.textContent=icons[_getTheme()];
})();
</script>
</body>
</html>
