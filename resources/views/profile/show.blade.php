@extends('layouts.app')
@section('title', 'My Profile — KZS 2002 Reunion')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-primary">My Profile</h1>
        <a href="{{ route('profile.edit') }}"
            class="bg-primary text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
            Edit Profile
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        {{-- Photo & name --}}
        <div class="flex items-center gap-5 mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                @if($alumni->photo_url)
                    <img src="{{ Storage::url($alumni->photo_url) }}" alt="{{ $alumni->name }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-3xl text-gray-400 dark:text-gray-500">
                        {{ strtoupper(substr($alumni->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $alumni->name }}</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">SSC Roll: {{ $alumni->roll_number }}</p>
                <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full mt-1">Verified</span>
            </div>
        </div>

        {{-- Info rows --}}
        <dl class="space-y-4 text-sm">
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Email</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->email }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Mobile</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->mobile ?: ($alumni->phone ?: '—') }}</dd>
            </div>
            @if($alumni->emergency_contact)
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Emergency Contact</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->emergency_contact }}</dd>
            </div>
            @endif
            @if($alumni->whatsapp)
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">WhatsApp</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->whatsapp }}</dd>
            </div>
            @endif
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Spouse</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->spouse_name ?: '—' }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Present Address</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->present_address ?: '—' }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Profession</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->current_profession ?: '—' }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Location</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->current_location ?: '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Event registration summary --}}
    @if($alumni->eventRegistration)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mt-4">
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">Event Registration</h3>
        <dl class="space-y-3 text-sm">
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Guests</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->eventRegistration->guest_count }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">T-Shirt Size</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->eventRegistration->tshirt_size ?: '—' }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Dietary Notes</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $alumni->eventRegistration->dietary_notes ?: '—' }}</dd>
            </div>
            <div class="flex gap-4">
                <dt class="w-40 text-gray-400 dark:text-gray-500 font-medium flex-shrink-0">Payment</dt>
                <dd>
                    <span class="capitalize font-semibold
                        {{ $alumni->eventRegistration->payment_status === 'paid' ? 'text-green-600' : 'text-orange-500' }}">
                        {{ $alumni->eventRegistration->payment_status }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>
    @endif
</div>
@endsection
