<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KZS 2002 Reunion')</title>
    <script>
    (function(){
        var t = localStorage.getItem('theme');
        if(t==='dark'||(t!=='light'&&window.matchMedia('(prefers-color-scheme:dark)').matches)){
            document.documentElement.classList.add('dark');
        }
    })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
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
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">

    <nav class="bg-white dark:bg-gray-800 border-b-4 border-brand shadow-sm dark:border-brand">
        <div class="max-w-5xl mx-auto px-4 py-2 flex items-center justify-between">
            <a href="{{ route('home') }}">
                <img src="/images/logo.jpg" alt="KZS 2002" class="h-10 w-auto">
            </a>
            <div class="flex items-center gap-2">
                {{-- Desktop nav links --}}
                <div class="hidden sm:flex items-center gap-4 text-sm">
                    @auth
                        <span class="text-kgray dark:text-gray-300 font-medium hidden sm:inline">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-red-700 transition">Admin Panel</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="text-kgray dark:text-gray-300 hover:text-kgreen transition font-medium">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button class="text-kgray dark:text-gray-300 hover:text-brand transition font-medium">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-kgray dark:text-gray-300 hover:text-kgreen transition font-medium">Login</a>
                        <a href="{{ route('register') }}"
                            class="bg-kgreen text-white font-semibold px-4 py-1.5 rounded-lg hover:opacity-90 transition text-sm">
                            Register
                        </a>
                    @endauth
                </div>
                {{-- Theme toggle (always visible) --}}
                <button id="themeBtn" onclick="cycleTheme()" title="Toggle theme"
                    class="text-lg w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    ⚙️
                </button>
                {{-- Mobile hamburger --}}
                <button class="sm:hidden w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-kgray dark:text-gray-300" onclick="toggleMobileMenu()">☰</button>
            </div>
        </div>
        {{-- Mobile dropdown --}}
        <div id="mobileMenu" class="hidden sm:hidden border-t border-gray-200 dark:border-gray-700 py-2 px-4 space-y-1 text-sm bg-white dark:bg-gray-800">
            @auth
                <div class="py-1.5 font-medium text-kgray dark:text-gray-300">{{ auth()->user()->name }}</div>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block py-1.5 text-primary font-bold">Admin Panel</a>
                @endif
                <a href="{{ route('dashboard') }}" class="block py-1.5 text-kgray dark:text-gray-300 hover:text-kgreen transition font-medium">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left py-1.5 text-kgray dark:text-gray-300 hover:text-brand transition font-medium">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block py-1.5 text-kgray dark:text-gray-300 hover:text-kgreen transition font-medium">Login</a>
                <a href="{{ route('register') }}" class="block py-1.5 text-kgray dark:text-gray-300 hover:text-kgreen transition font-medium">Register</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-300 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700 text-blue-800 dark:text-blue-300 rounded-lg px-4 py-3 text-sm">
                {{ session('info') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-300 rounded-lg px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-gray-400 dark:text-gray-500 text-xs py-6 border-t border-gray-200 dark:border-gray-700 mt-8">
        <img src="/images/logo.jpg" alt="KZS 2002" class="h-6 w-auto mx-auto mb-2 opacity-40">
        KZS 2002 SSC Batch &mdash; 25-Year Reunion &copy; {{ date('Y') }}
    </footer>

<script>
function toggleMobileMenu(){document.getElementById('mobileMenu').classList.toggle('hidden');}
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
