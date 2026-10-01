@extends('layouts.auth')
@section('title', 'Sign In — KZS 2002 Reunion')

@section('form')

<h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-1">Welcome back</h1>
<p class="text-gray-500 dark:text-gray-400 text-sm mb-7">Sign in to your KZS 2002 Reunion account</p>

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    {{-- Identifier --}}
    <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">
            Email / Mobile / Roll number <span class="text-brand">*</span>
        </label>
        <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
               placeholder="e.g. your@email.com or 01XXXXXXXXX"
               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400
                      focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent
                      @error('identifier') border-red-400 @enderror">
        @error('identifier')
            <p class="text-brand text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Password <span class="text-brand">*</span></label>
        <div class="relative">
            <input type="password" name="password" required id="login_password"
                   placeholder="Your password"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400
                          focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent pr-10
                          @error('password') border-red-400 @enderror">
            <button type="button" onclick="togglePwd('login_password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-5 0-9-4-9-7s4-7 9-7a9.97 9.97 0 016.364 2.273M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                </svg>
            </button>
        </div>
        @error('password')
            <p class="text-brand text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Remember me --}}
    <div class="flex items-center gap-2">
        <input type="checkbox" name="remember" id="remember"
               class="rounded border-gray-300 accent-indigo-500 cursor-pointer">
        <label for="remember" class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">Remember me</label>
    </div>

    <button type="submit"
            class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2.5 rounded-lg transition text-sm">
        Sign in
    </button>
</form>

<p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-5">
    New alumni?
    <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:underline">Create an account</a>
</p>

<script>
function togglePwd(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

@endsection
