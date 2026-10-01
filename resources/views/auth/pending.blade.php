@extends('layouts.app')
@section('title', 'Pending Approval — KZS 2002 Reunion')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 text-center">
        <div class="text-5xl mb-4">⏳</div>
        <h1 class="text-2xl font-bold text-primary mb-2">Awaiting Approval</h1>
        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6">
            Your email has been verified. A reunion committee member will review and approve your
            account shortly. You will be able to log in once approved.
        </p>
        <p class="text-gray-500 dark:text-gray-400 text-xs">
            Questions? Contact us on WhatsApp or reach out to the reunion committee.
        </p>
        <a href="{{ route('home') }}" class="inline-block mt-6 text-primary text-sm font-medium hover:underline">
            &larr; Back to Home
        </a>
    </div>
</div>
@endsection
