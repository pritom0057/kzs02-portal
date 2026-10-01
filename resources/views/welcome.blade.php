<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KZS 2002 — Silver Jubilee Reunion</title>
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
                        brand:  '#c0392b',
                        kgreen: '#27ae60',
                        kgray:  '#2c3e50',
                    }
                }
            }
        }
    </script>
    <style>
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px #fff inset; }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900">

<div class="min-h-screen flex flex-col lg:flex-row">

    {{-- ── LEFT PANEL ─────────────────────────────────────── --}}
    <div class="relative w-full lg:w-[52%] flex flex-col justify-center px-6 sm:px-12 xl:px-20 py-14 bg-white dark:bg-gray-800">

        {{-- Theme toggle (top-right of left panel) --}}
        <div class="absolute top-4 right-4">
            <button id="themeBtn" onclick="cycleTheme()" title="Toggle theme"
                class="text-lg w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                ⚙️
            </button>
        </div>

        <div class="max-w-md w-full mx-auto">

            {{-- Logo + Badge --}}
            <div class="flex items-center gap-3 mb-10">
                <img src="/images/logo.jpg" alt="KZS 2002" class="h-14 w-auto rounded-xl shadow-sm">
                <div>
                    <p class="text-[11px] font-bold text-brand uppercase tracking-widest">Silver Jubilee</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">KZS · SSC Batch 2002</p>
                </div>
            </div>

            {{-- Heading --}}
            <h1 class="text-4xl sm:text-5xl font-black text-kgray dark:text-gray-100 leading-tight mb-2">
                25 বছর পর<br>
                <span class="text-brand">আবার একসাথে</span>
            </h1>
            <p class="text-gray-400 dark:text-gray-500 text-sm mb-8 leading-relaxed">
                Kushtia Zilla School — SSC Batch 2002<br>
                Celebrating 25 years of friendship &amp; unity
            </p>

            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 rounded-xl px-4 py-3 text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 rounded-xl px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @auth
            {{-- Already logged in --}}
            <div class="space-y-3">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center justify-center gap-2 w-full bg-brand hover:bg-red-700 text-white font-bold py-3.5 rounded-xl transition text-sm shadow-sm">
                    Go to Dashboard →
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full border-2 border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500 hover:text-gray-700 dark:hover:text-gray-200 font-semibold py-3 rounded-xl transition text-sm">
                        Logout
                    </button>
                </form>
            </div>

            @else
            {{-- ── LOGIN FORM ── --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">
                        Email / Mobile / Roll Number
                    </label>
                    <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                           placeholder="your@email.com or 01XXXXXXXXX"
                           class="w-full border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300
                                  focus:outline-none focus:border-brand transition
                                  @error('identifier') border-red-400 @enderror">
                    @error('identifier')
                        <p class="text-brand text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5 uppercase tracking-wide">Password</label>
                    <div class="relative">
                        <input type="password" name="password" required id="login_password"
                               placeholder="Your password"
                               class="w-full border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300
                                      focus:outline-none focus:border-brand transition pr-11
                                      @error('password') border-red-400 @enderror">
                        <button type="button" onclick="togglePwd()"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-brand text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 accent-brand cursor-pointer">
                        <span class="text-sm text-gray-500 dark:text-gray-400 select-none">Remember me</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-brand hover:bg-red-700 text-white font-bold py-3.5 rounded-xl transition text-sm shadow-sm">
                    Sign In to Portal
                </button>
            </form>

            <div class="mt-5 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    New alumni?
                    <a href="{{ route('register') }}" class="text-brand font-semibold hover:underline">Create an account</a>
                </p>
            </div>
            @endauth

            {{-- Stats --}}
            <div class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-700 grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-black text-brand">2002</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 uppercase tracking-wide">SSC Batch</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-kgray dark:text-gray-100">25</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 uppercase tracking-wide">Years Later</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-kgreen">KZS</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 uppercase tracking-wide">Kushtia</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ── RIGHT PANEL ─────────────────────────────────────── --}}
    <div class="hidden lg:block lg:w-[48%] relative overflow-hidden">
        <img src="/images/auth-bg.jpg" alt="KZS Reunion"
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/10"></div>
        <div class="absolute bottom-8 left-8 right-8 text-white">
            <p class="text-xs font-semibold uppercase tracking-widest text-white/70 mb-1">Silver Jubilee Reunion</p>
            <p class="text-2xl font-black leading-snug">Celebrating Unity<br>Remembering Beginnings</p>
        </div>
    </div>

</div>

<script>
function togglePwd() {
    const input = document.getElementById('login_password');
    input.type = input.type === 'password' ? 'text' : 'password';
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
