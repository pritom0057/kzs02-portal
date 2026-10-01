@extends('layouts.auth')
@section('title', 'Create Account — KZS 2002 Reunion')

@section('form')

<h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-1">Create an account</h1>
<p class="text-gray-500 dark:text-gray-400 text-sm mb-7">To continue, fill out your personal info</p>

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    {{-- Full Name --}}
    <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Full name <span class="text-brand">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}" required autofocus
               placeholder="Name Surname"
               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400
                      focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent
                      @error('name') border-red-400 @enderror">
        @error('name')
            <p class="text-brand text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">E-mail <span class="text-brand">*</span></label>
        <input type="email" name="email" value="{{ old('email') }}" required
               placeholder="email@email.com"
               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400
                      focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent
                      @error('email') border-red-400 @enderror">
        @error('email')
            <p class="text-brand text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Mobile --}}
    <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">
            Mobile number <span class="text-gray-400 font-normal">(optional)</span>
        </label>
        <input type="text" name="mobile" value="{{ old('mobile') }}"
               placeholder="01XXXXXXXXX"
               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400
                      focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
    </div>

    {{-- SSC Roll --}}
    <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">
            SSC Roll number <span class="text-gray-400 font-normal">(optional)</span>
        </label>
        <input type="text" name="roll_number" value="{{ old('roll_number') }}"
               placeholder="Leave blank if you don't remember"
               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400
                      focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent
                      @error('roll_number') border-red-400 @enderror">
        @error('roll_number')
            <p class="text-brand text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Password <span class="text-brand">*</span></label>
        <div class="relative">
            <input type="password" name="password" required id="reg_password"
                   placeholder="Min. 8 characters"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400
                          focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent pr-10
                          @error('password') border-red-400 @enderror">
            <button type="button" onclick="togglePwd('reg_password', this)"
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

    {{-- Confirm Password --}}
    <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Repeat password <span class="text-brand">*</span></label>
        <div class="relative">
            <input type="password" name="password_confirmation" required id="reg_confirm"
                   placeholder="Repeat your password"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400
                          focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent pr-10">
            <button type="button" onclick="togglePwd('reg_confirm', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-5 0-9-4-9-7s4-7 9-7a9.97 9.97 0 016.364 2.273M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                </svg>
            </button>
        </div>
    </div>

    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
        By clicking Continue, you agree to our
        <a href="#" class="underline hover:text-gray-700 dark:hover:text-gray-200">Terms and Conditions</a>,
        confirm you have read our
        <a href="#" class="underline hover:text-gray-700 dark:hover:text-gray-200">Privacy Policy</a>.
    </p>

    <button type="submit"
            class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2.5 rounded-lg transition text-sm mt-1">
        Sign up
    </button>
</form>

<p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-5">
    Already registered?
    <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">Sign in</a>
</p>

<script>
function togglePwd(id, btn) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

@endsection
