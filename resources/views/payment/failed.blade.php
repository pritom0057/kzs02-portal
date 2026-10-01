@extends('layouts.app')
@section('title', 'Payment Failed — KZS 2002 Reunion')

@section('content')
<div class="max-w-md mx-auto text-center py-8">
    <div class="text-6xl mb-4">{{ $reason === 'cancelled' ? '🚫' : '❌' }}</div>
    <h1 class="text-2xl font-bold text-red-600 dark:text-red-400 mb-2">
        {{ $reason === 'cancelled' ? 'Payment Cancelled' : 'Payment Failed' }}
    </h1>
    <p class="text-gray-600 dark:text-gray-400 mb-8">
        @if($reason === 'cancelled')
            You cancelled the payment. Your registration is saved — you can try again anytime.
        @else
            Something went wrong with your payment. Please try again or contact your bank.
        @endif
    </p>

    <div class="flex justify-center gap-3">
        <a href="{{ route('payment.confirm') }}"
            class="bg-primary text-white font-semibold px-6 py-2 rounded-lg hover:bg-opacity-90 transition">
            Try Again
        </a>
        <a href="{{ route('dashboard') }}"
            class="border-2 border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 font-semibold px-6 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
            Dashboard
        </a>
    </div>
</div>
@endsection
