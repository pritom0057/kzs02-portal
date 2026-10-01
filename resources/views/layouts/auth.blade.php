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
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#c0392b',
                        brand:  '#c0392b',
                        kgreen: '#27ae60',
                        kgray:  '#555555',
                    }
                }
            }
        }
    </script>
    <style>
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px #fff inset; }
    </style>
</head>
<body class="min-h-screen bg-white dark:bg-gray-900">

<div class="flex min-h-screen">

    {{-- Left: form panel --}}
    <div class="relative w-full lg:w-[52%] flex flex-col justify-center px-8 sm:px-14 xl:px-20 py-12 bg-white dark:bg-gray-800">

        {{-- Theme toggle button (top-right of left panel) --}}
        <div class="absolute top-4 right-4">
            <button id="themeBtn" onclick="cycleTheme()" title="Toggle theme"
                class="text-lg w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                ⚙️
            </button>
        </div>

        <div class="max-w-sm w-full mx-auto">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-5 bg-green-50 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-300 rounded-lg px-4 py-3 text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-300 rounded-lg px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="mb-5 bg-blue-50 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-700 text-blue-800 dark:text-blue-300 rounded-lg px-4 py-3 text-sm">
                    {{ session('info') }}
                </div>
            @endif

            @yield('form')

        </div>
    </div>

    {{-- Right: photo panel --}}
    <div class="hidden lg:block lg:w-[48%] relative overflow-hidden">
        <img src="/images/auth-bg.jpg" alt="KZS Reunion"
             class="absolute inset-0 w-full h-full object-cover">
        {{-- gradient overlay so the photo never clashes with white text if added later --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/10 to-teal-700/10"></div>
    </div>

</div>

<script>
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
