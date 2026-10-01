@extends('layouts.app')
@section('title', 'Confirm Payment — KZS 2002 Reunion')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Confirm Payment</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-4">
        <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-4">Order Summary</p>

        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">Alumnus</dt>
                <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $alumni->name }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">Roll Number</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->roll_number }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">T-Shirt Size</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $registration->tshirt_size }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">Additional Guests</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $registration->guest_count }}</dd>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700 pt-3 flex justify-between">
                <dt class="font-semibold text-gray-700 dark:text-gray-300">Registration Fee</dt>
                <dd class="font-bold text-primary text-lg">৳ {{ number_format($fee, 0) }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-xl p-4 text-sm text-blue-700 dark:text-blue-300 mb-4">
        You will be redirected to SSLCommerz to pay securely via bKash, Nagad, card, or internet banking.
    </div>

    <form method="POST" action="{{ route('payment.initiate') }}">
        @csrf
        <button type="submit"
            class="w-full bg-primary text-white font-semibold py-3 rounded-xl hover:bg-opacity-90 transition text-base">
            Pay ৳ {{ number_format($fee, 0) }} Now
        </button>
    </form>

    <a href="{{ route('dashboard') }}" class="block text-center text-sm text-gray-400 dark:text-gray-500 hover:text-primary mt-4 transition">
        Pay later
    </a>
</div>
@endsection
