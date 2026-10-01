@extends('layouts.app')
@section('title', 'Verify Email — KZS 2002 Reunion')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 text-center">
        <div class="text-5xl mb-4">📧</div>
        <h1 class="text-2xl font-bold text-primary mb-1">Check Your Email</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">
            We sent a 6-digit code to <strong>{{ session('otp_email') }}</strong>
        </p>

        <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
            @csrf

            <div>
                <input type="text" name="otp" maxlength="6" inputmode="numeric" autofocus
                    class="w-full border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-3 text-center text-2xl tracking-widest font-bold focus:outline-none focus:border-primary @error('otp') border-red-400 @enderror"
                    placeholder="000000">
                @error('otp')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-primary text-white font-semibold py-2 rounded-lg hover:bg-opacity-90 transition">
                Verify Code
            </button>
        </form>

        <form method="POST" action="{{ route('otp.resend') }}" class="mt-4">
            @csrf
            <button type="submit" class="text-sm text-primary hover:underline">
                Didn't receive the code? Resend
            </button>
        </form>
    </div>
</div>
@endsection
