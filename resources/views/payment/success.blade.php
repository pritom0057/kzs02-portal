@extends('layouts.app')
@section('title', 'Payment Successful — KZS 2002 Reunion')

@section('content')
<div class="max-w-md mx-auto text-center py-8">
    <div class="text-6xl mb-4">✅</div>
    <h1 class="text-2xl font-bold text-green-700 dark:text-green-400 mb-2">Payment Successful!</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-2">
        Thank you, <strong>{{ $alumni->name }}</strong>. Your registration is confirmed.
    </p>
    <p class="text-gray-500 dark:text-gray-400 text-sm mb-8">
        You will receive a confirmation email. See you at the reunion!
    </p>

    <div class="flex justify-center gap-3">
        <a href="{{ route('dashboard') }}"
            class="bg-primary text-white font-semibold px-6 py-2 rounded-lg hover:bg-opacity-90 transition">
            Go to Dashboard
        </a>
        <a href="{{ route('directory') }}"
            class="border-2 border-primary text-primary font-semibold px-6 py-2 rounded-lg hover:bg-primary hover:text-white transition">
            View Directory
        </a>
    </div>
</div>
@endsection
