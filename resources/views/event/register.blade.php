@extends('layouts.app')
@section('title', 'Event Registration — KZS 2002 Reunion')

@section('content')
@php
    $reg = $registration;
    $childrenDetails = $reg?->children_details ?? [];
    $guestsDetails   = $reg?->guests_details ?? [];
@endphp

<div class="max-w-5xl mx-auto">

  {{-- Banner --}}
  <div class="rounded-2xl overflow-hidden mb-6 shadow">
    <img src="{{ asset('images/cover.jpg') }}" alt="KZS 2002 Silver Jubilee Reunion" class="w-full object-cover">
  </div>

  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-primary">Silver Jubilee Event Registration</h1>
      <p class="text-gray-400 dark:text-gray-500 text-sm">KZS 2002 SSC Batch — Celebrating 25 Years of Unity</p>
    </div>
    <a href="{{ route('dashboard') }}" class="text-sm text-gray-400 dark:text-gray-500 hover:text-kgreen transition">&larr; Dashboard</a>
  </div>

  @if($reg)
  <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 text-sm rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
    <i class="fa-solid fa-circle-check"></i>
    You are already registered. Update your details below.
    @if($reg->payment_status === 'paid')
      <span class="ml-auto font-bold text-green-800 dark:text-green-200">✅ Payment Confirmed — ৳{{ number_format($reg->total_amount, 0) }}</span>
    @endif
  </div>
  @endif

  <form method="POST" action="{{ route('event.save') }}" enctype="multipart/form-data" class="space-y-5" id="regForm">
    @csrf

    {{-- SECTION 1: Schooling --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
      <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-gray-100 dark:border-gray-700">
        <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/30 text-primary flex items-center justify-center"><i class="fa-solid fa-school text-sm"></i></div>
        <div>
          <h2 class="text-sm font-bold text-kgray dark:text-gray-200 uppercase tracking-wide">1. Schooling Information at KZS</h2>
          <p class="text-xs text-gray-400 dark:text-gray-500">Your class and shift records at Kushtia Zilla School Batch '02</p>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Studied Up To <span class="text-brand">*</span></label>
          <select name="school_class" required class="w-full border @error('school_class') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
            <option value="" disabled {{ !old('school_class', $alumni->school_class) ? 'selected' : '' }}>Select Class</option>
            @foreach(['Class 10 (Completed SSC 2002)','Class 9','Class 8','Class 7','Class 6','Class 5','Class 4','Class 3'] as $cls)
              <option value="{{ $cls }}" {{ old('school_class', $alumni->school_class) === $cls ? 'selected' : '' }}>{{ $cls }}</option>
            @endforeach
          </select>
          @error('school_class') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">School Shift <span class="text-brand">*</span></label>
          <div class="flex gap-2">
            @foreach(['Morning' => 'Morning', 'Day' => 'Day'] as $val => $label)
            <label class="cursor-pointer flex-1">
              <input type="radio" name="school_shift" value="{{ $val }}" class="sr-only peer"
                {{ old('school_shift', $alumni->school_shift) === $val ? 'checked' : '' }} required>
              <span class="block text-center border-2 border-gray-200 dark:border-gray-600 dark:text-gray-300 rounded-lg px-3 py-2.5 text-sm font-semibold peer-checked:border-kgreen peer-checked:text-kgreen peer-checked:bg-green-50 dark:peer-checked:bg-green-900/30 transition select-none">{{ $label }}</span>
            </label>
            @endforeach
          </div>
          @error('school_shift') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Section <span class="text-brand">*</span></label>
          <select name="school_section" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
            <option value="B" {{ old('school_section', $alumni->section) !== 'A' ? 'selected' : '' }}>Section B (Batch Main)</option>
            <option value="A" {{ old('school_section', $alumni->section) === 'A' ? 'selected' : '' }}>Section A</option>
          </select>
        </div>
      </div>
    </div>

    {{-- SECTION 2: Personal & Professional --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
      <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-gray-100 dark:border-gray-700">
        <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/30 text-primary flex items-center justify-center"><i class="fa-solid fa-user-graduate text-sm"></i></div>
        <div>
          <h2 class="text-sm font-bold text-kgray dark:text-gray-200 uppercase tracking-wide">2. Applicant Personal &amp; Professional Info</h2>
          <p class="text-xs text-gray-400 dark:text-gray-500">Identity, bilingual name, T-shirt size, and professional details</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name (English)</label>
          <input type="text" value="{{ $alumni->name }}" disabled
            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
          <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Edit via <a href="{{ route('profile.edit') }}" class="text-kgreen underline">Profile page</a></p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">সম্পূর্ণ নাম (বাংলায়) <span class="text-gray-400 font-normal text-xs">(optional)</span></label>
          <input type="text" name="name_bn" value="{{ old('name_bn', $alumni->name_bn) }}"
            placeholder="যেমন: মো: ইমরুল হাসান" style="font-family: 'Tiro Bangla', serif"
            class="w-full border @error('name_bn') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          @error('name_bn') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><i class="fa-brands fa-whatsapp text-green-600 mr-1"></i>Mobile &amp; WhatsApp <span class="text-brand">*</span></label>
          <input type="tel" name="mobile" required value="{{ old('mobile', $alumni->mobile ?? $alumni->phone) }}"
            placeholder="+880 17XXXXXXXX"
            class="w-full border @error('mobile') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          @error('mobile') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><i class="fa-solid fa-phone-volume text-red-400 mr-1"></i>Emergency Contact <span class="text-brand">*</span></label>
          <input type="tel" name="emergency_contact" required value="{{ old('emergency_contact', $alumni->emergency_contact) }}"
            placeholder="+880 18XXXXXXXX"
            class="w-full border @error('emergency_contact') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          @error('emergency_contact') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Highest Educational Qualification <span class="text-brand">*</span></label>
          <input type="text" name="higher_education" required value="{{ old('higher_education', $alumni->higher_education) }}"
            placeholder="e.g. B.Sc in EEE (BUET) / MBA (DU) / MBBS"
            class="w-full border @error('higher_education') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          @error('higher_education') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- T-Shirt Size --}}
      <div class="mb-5">
        <div class="flex items-center justify-between mb-2">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300"><i class="fa-solid fa-shirt text-primary mr-1"></i> Reunion T-Shirt Size <span class="text-brand">*</span></label>
          <span class="text-xs text-gray-400 dark:text-gray-500">Chest measurement (inches)</span>
        </div>
        <div class="flex flex-wrap gap-2">
          @foreach(['S' => '36"-38"','M' => '38"-40"','L' => '40"-42"','XL' => '42"-44"','XXL' => '44"-46"','XXXL' => '46"-48"'] as $size => $range)
          <label class="cursor-pointer">
            <input type="radio" name="tshirt_size" value="{{ $size }}" class="sr-only peer"
              {{ old('tshirt_size', $reg?->tshirt_size ?? 'M') === $size ? 'checked' : '' }}>
            <span class="inline-block border-2 border-gray-200 dark:border-gray-600 dark:text-gray-300 rounded-lg px-3 py-2 text-sm font-semibold peer-checked:border-kgreen peer-checked:text-kgreen peer-checked:bg-green-50 dark:peer-checked:bg-green-900/30 transition select-none">
              {{ $size }} <span class="text-xs font-normal text-gray-400 dark:text-gray-500">{{ $range }}</span>
            </span>
          </label>
          @endforeach
        </div>
        @error('tshirt_size') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Professional --}}
      <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-1.5"><i class="fa-solid fa-briefcase"></i> Professional Information</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Designation / Title <span class="text-brand">*</span></label>
            <input type="text" name="designation" required value="{{ old('designation', $alumni->designation) }}"
              placeholder="e.g. Senior Software Engineer / Manager"
              class="w-full border @error('designation') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen bg-white dark:bg-gray-700 dark:text-gray-200">
            @error('designation') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Organization <span class="text-brand">*</span></label>
            <input type="text" name="organization" required value="{{ old('organization', $alumni->organization) }}"
              placeholder="e.g. Grameenphone / Tech Solutions Ltd."
              class="w-full border @error('organization') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen bg-white dark:bg-gray-700 dark:text-gray-200">
            @error('organization') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- SECTION 3: Family & Guests --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
      <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-gray-100 dark:border-gray-700">
        <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/30 text-primary flex items-center justify-center"><i class="fa-solid fa-people-roof text-sm"></i></div>
        <div>
          <h2 class="text-sm font-bold text-kgray dark:text-gray-200 uppercase tracking-wide">3. Family &amp; Accompanying Persons</h2>
          <p class="text-xs text-gray-400 dark:text-gray-500">ব্যাচের সদস্য (স্ত্রী ও সন্তান সহ) — ৳২,০০২ · গেস্ট (জনপ্রতি) — ৳১,০০০ · ড্রাইভার — ৳৫০০</p>
        </div>
      </div>

      <div class="space-y-5">
        {{-- Spouse --}}
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
          <label class="flex items-center gap-3 cursor-pointer mb-3">
            <input type="checkbox" name="bring_spouse" id="bring_spouse" value="1" class="w-4 h-4 accent-kgreen"
              onchange="calcFees()" {{ old('bring_spouse', $reg?->bring_spouse) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><i class="fa-solid fa-heart text-red-400 mr-1"></i>Spouse Attending <span class="text-gray-400 dark:text-gray-500 font-normal">(included in base fee)</span></span>
          </label>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Spouse Name (English)</label>
              <input type="text" name="spouse_name" value="{{ old('spouse_name', $reg?->spouse_name ?? $alumni->spouse_name) }}"
                placeholder="Spouse full name"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen bg-white dark:bg-gray-700 dark:text-gray-200">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">স্ত্রীর নাম (বাংলায়)</label>
              <input type="text" name="spouse_name_bn" value="{{ old('spouse_name_bn', $reg?->spouse_name_bn) }}"
                placeholder="স্ত্রীর সম্পূর্ণ নাম" style="font-family: 'Tiro Bangla', serif"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen bg-white dark:bg-gray-700 dark:text-gray-200">
            </div>
          </div>
        </div>

        {{-- Children (dynamic) --}}
        <div>
          <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300"><i class="fa-solid fa-child text-primary mr-1"></i>Children Attending <span class="text-gray-400 dark:text-gray-500 font-normal text-xs">(included in base fee)</span></p>
            <button type="button" id="btnAddChild"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 text-xs font-semibold transition">
              <i class="fa-solid fa-plus"></i> Add Child
            </button>
          </div>
          <div id="childrenContainer" class="space-y-2.5">
            {{-- Pre-fill from saved data --}}
            @foreach($childrenDetails as $i => $child)
            <div class="child-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-3 space-y-2.5">
              <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                <span class="text-xs font-bold text-kgray dark:text-gray-300 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-child text-primary"></i> Child Details</span>
                <button type="button" class="remove-child text-xs font-semibold text-red-500 hover:text-red-700 flex items-center gap-1"><i class="fa-solid fa-trash-can"></i> Remove</button>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Name (English) <span class="text-brand">*</span></label>
                  <input type="text" name="children_details[{{ $i }}][name]" value="{{ $child['name'] ?? '' }}" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="e.g. Abrar Hasan"></div>
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">নাম (বাংলায়)</label>
                  <input type="text" name="children_details[{{ $i }}][name_bn]" value="{{ $child['name_bn'] ?? '' }}" style="font-family:'Tiro Bangla',serif" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="যেমন: আবরার হাসান"></div>
              </div>
            </div>
            @endforeach
          </div>
          <div id="emptyChildrenNotice" class="{{ count($childrenDetails) > 0 ? 'hidden' : '' }} text-center py-4 text-xs text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-200 dark:border-gray-600">
            No children added. Click <strong>+ Add Child</strong> if bringing kids.
          </div>
        </div>

        {{-- Guests (dynamic) --}}
        <div>
          <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300"><i class="fa-solid fa-users text-primary mr-1"></i>Additional Guests <span class="text-gray-400 dark:text-gray-500 font-normal text-xs">(+৳1,000 each)</span></p>
            <button type="button" id="btnAddGuest"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 text-xs font-semibold transition">
              <i class="fa-solid fa-user-plus"></i> Add Guest
            </button>
          </div>
          <div id="guestsContainer" class="space-y-2.5">
            @foreach($guestsDetails as $i => $guest)
            <div class="guest-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-3 space-y-2.5">
              <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                <span class="text-xs font-bold text-kgray dark:text-gray-300 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-user-plus text-primary"></i> Guest Details</span>
                <button type="button" class="remove-guest text-xs font-semibold text-red-500 hover:text-red-700 flex items-center gap-1"><i class="fa-solid fa-trash-can"></i> Remove</button>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Full Name <span class="text-brand">*</span></label>
                  <input type="text" name="guests_details[{{ $i }}][name]" value="{{ $guest['name'] ?? '' }}" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="Full name"></div>
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Relationship</label>
                  <input type="text" name="guests_details[{{ $i }}][relationship]" value="{{ $guest['relationship'] ?? '' }}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="e.g. Brother / Cousin"></div>
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Contact Number</label>
                  <input type="tel" name="guests_details[{{ $i }}][contact]" value="{{ $guest['contact'] ?? '' }}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="+880 1XXXXXXXXX"></div>
              </div>
            </div>
            @endforeach
          </div>
          <div id="emptyGuestsNotice" class="{{ count($guestsDetails) > 0 ? 'hidden' : '' }} text-center py-4 text-xs text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-200 dark:border-gray-600">
            No guests added. Click <strong>+ Add Guest</strong> to add.
          </div>
        </div>

        {{-- Driver --}}
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="driver_included" id="driver_included" value="1" class="w-4 h-4 accent-kgreen"
              onchange="calcFees()" {{ old('driver_included', $reg?->driver_included) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><i class="fa-solid fa-car text-gray-400 mr-1"></i>Driver Entry &amp; Meal Pack <span class="text-gray-400 dark:text-gray-500 font-normal">(+৳500)</span></span>
          </label>
        </div>

        {{-- Donation --}}
        <div class="border border-dashed border-kgreen/40 rounded-xl p-4 bg-green-50/40 dark:bg-green-900/10">
          <div class="flex items-center gap-2 mb-3">
            <i class="fa-solid fa-hand-holding-heart text-kgreen"></i>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Donation</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-kgray dark:text-gray-300">৳</span>
            <input type="text" name="donation_amount" id="donation_amount"
              value="{{ old('donation_amount', $reg?->donation_amount > 0 ? $reg->donation_amount : '') }}"
              oninput="calcFees()"
              class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen bg-white dark:bg-gray-700 dark:text-gray-200">
          </div>
        </div>
      </div>
    </div>

    {{-- SECTION 4: Address --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
      <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-gray-100 dark:border-gray-700">
        <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/30 text-primary flex items-center justify-center"><i class="fa-solid fa-map-location-dot text-sm"></i></div>
        <div>
          <h2 class="text-sm font-bold text-kgray dark:text-gray-200 uppercase tracking-wide">4. Present &amp; Permanent Address</h2>
          <p class="text-xs text-gray-400 dark:text-gray-500">Complete address with Thana, Upazila, and District</p>
        </div>
      </div>

      @php
        // Format: "{road}, PO: {post}, {thana}, PC: {postcode}, {district}"
        // Also handles legacy "Upazila:" prefix for existing data
        function parseAddr(string $addr): array {
            $parts = ['road'=>'','post'=>'','thana'=>'','postcode'=>'','district'=>''];
            if (preg_match('/^(.*?),\s*PO:\s*(.*?),\s*(.*?),\s*(?:PC:|Upazila:)\s*(.*?),\s*(.*)$/', $addr, $m)) {
                $parts = ['road'=>trim($m[1]),'post'=>trim($m[2]),'thana'=>trim($m[3]),'postcode'=>trim($m[4]),'district'=>trim($m[5])];
            }
            return $parts;
        }
        $pres = parseAddr($alumni->present_address ?? '');
        $perm = parseAddr($alumni->permanent_address ?? '');
      @endphp

      {{-- Present Address --}}
      <div class="mb-6">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Present Address</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
          <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Village / Road / House <span class="text-brand">*</span></label>
            <input type="text" name="pres_road" required value="{{ old('pres_road', $pres['road']) }}" id="pres_road"
              placeholder="House 12, Road 4, Court Para"
              class="w-full border @error('pres_road') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Post Office <span class="text-brand">*</span></label>
            <input type="text" name="pres_post" required value="{{ old('pres_post', $pres['post']) }}" id="pres_post"
              placeholder="e.g. Kushtia Head PO"
              class="w-full border @error('pres_post') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Thana <span class="text-brand">*</span></label>
            <input type="text" name="pres_thana" required value="{{ old('pres_thana', $pres['thana']) }}" id="pres_thana"
              placeholder="e.g. Kushtia Sadar"
              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Post Code <span class="text-brand">*</span></label>
            <input type="text" name="pres_postcode" required value="{{ old('pres_postcode', $pres['postcode']) }}" id="pres_postcode"
              placeholder="e.g. 7000"
              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">District <span class="text-brand">*</span></label>
            <input type="text" name="pres_district" required value="{{ old('pres_district', $pres['district']) }}" id="pres_district"
              placeholder="Kushtia"
              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
        </div>
      </div>

      {{-- Permanent Address --}}
      <div>
        <div class="flex items-center justify-between mb-3">
          <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Permanent Address</p>
          <label class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300 cursor-pointer bg-gray-50 dark:bg-gray-700 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
            <input type="checkbox" id="sameAddr" class="w-3.5 h-3.5 accent-kgreen"> Same as Present Address
          </label>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
          <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Village / Road / House <span class="text-brand">*</span></label>
            <input type="text" name="perm_road" required value="{{ old('perm_road', $perm['road']) }}" id="perm_road"
              placeholder="Village / Road"
              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Post Office <span class="text-brand">*</span></label>
            <input type="text" name="perm_post" required value="{{ old('perm_post', $perm['post']) }}" id="perm_post"
              placeholder="Post Office"
              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Thana <span class="text-brand">*</span></label>
            <input type="text" name="perm_thana" required value="{{ old('perm_thana', $perm['thana']) }}" id="perm_thana"
              placeholder="Thana"
              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Post Code <span class="text-brand">*</span></label>
            <input type="text" name="perm_postcode" required value="{{ old('perm_postcode', $perm['postcode']) }}" id="perm_postcode"
              placeholder="e.g. 7000"
              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">District <span class="text-brand">*</span></label>
            <input type="text" name="perm_district" required value="{{ old('perm_district', $perm['district']) }}" id="perm_district"
              placeholder="District"
              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
          </div>
        </div>
      </div>
    </div>

    {{-- SECTION 5: Photos --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
      <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-gray-100 dark:border-gray-700">
        <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/30 text-primary flex items-center justify-center"><i class="fa-solid fa-camera text-sm"></i></div>
        <div>
          <h2 class="text-sm font-bold text-kgray dark:text-gray-200 uppercase tracking-wide">5. Photographs Upload</h2>
          <p class="text-xs text-gray-400 dark:text-gray-500">Passport photo and optional family picture for souvenir album</p>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Passport Photo --}}
        <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-xl p-5 text-center hover:border-primary transition group">
          @if($alumni->photo_url)
          @php $photoSrc = str_starts_with($alumni->photo_url, 'uploads/') ? asset($alumni->photo_url) : asset('storage/' . $alumni->photo_url); @endphp
          <div class="mb-3"><img src="{{ $photoSrc }}" id="passport_preview" alt="Current Photo" class="w-24 h-28 mx-auto object-cover rounded-xl border-2 border-gray-300 dark:border-gray-600 shadow"></div>
          @else
          <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/30 text-primary flex items-center justify-center mx-auto mb-2" id="passport_icon"><i class="fa-solid fa-user-tie"></i></div>
          @endif
          <p class="font-semibold text-sm text-gray-700 dark:text-gray-300">Passport Photo <span class="text-gray-400 dark:text-gray-500 font-normal text-xs">({{ $alumni->photo_url ? 'update' : 'required' }})</span></p>
          <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Clear headshot · JPG/PNG · max 3MB</p>
          <input type="file" name="passport_photo" id="passport_photo" accept="image/*" {{ !$alumni->photo_url ? 'required' : '' }}
            class="hidden" onchange="previewUpload(this,'passport_preview','passport_icon')">
          <label for="passport_photo" class="mt-3 inline-block cursor-pointer px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold group-hover:bg-red-50 group-hover:border-primary group-hover:text-primary transition">Browse Image</label>
        </div>
        {{-- Family Photo --}}
        <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-xl p-5 text-center hover:border-primary transition group">
          @if($alumni->family_photo_url)
          @php $familySrc = str_starts_with($alumni->family_photo_url, 'uploads/') ? asset($alumni->family_photo_url) : asset('storage/' . $alumni->family_photo_url); @endphp
          <div class="mb-3"><img src="{{ $familySrc }}" id="family_preview" alt="Family Photo" class="w-36 h-28 mx-auto object-cover rounded-xl border-2 border-gray-300 dark:border-gray-600 shadow"></div>
          @else
          <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/30 text-primary flex items-center justify-center mx-auto mb-2" id="family_icon"><i class="fa-solid fa-users-viewfinder"></i></div>
          @endif
          <p class="font-semibold text-sm text-gray-700 dark:text-gray-300">Family Picture <span class="text-gray-400 dark:text-gray-500 font-normal">(Optional)</span></p>
          <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">For the Reunion Souvenir Album</p>
          <input type="file" name="family_photo" id="family_photo" accept="image/*" class="hidden"
            onchange="previewUpload(this,'family_preview','family_icon')">
          <label for="family_photo" class="mt-3 inline-block cursor-pointer px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold group-hover:bg-red-50 group-hover:border-primary group-hover:text-primary transition">Browse Image</label>
        </div>
      </div>
    </div>

    {{-- SECTION 6: Fee Summary (bg-kgray is already dark — left as-is) --}}
    <div class="bg-kgray text-white rounded-xl p-5">
      <p class="text-xs font-semibold text-white/60 uppercase tracking-wide mb-3 flex items-center gap-1.5"><i class="fa-solid fa-calculator"></i> Registration Fee</p>
      <div class="space-y-2 text-sm">
        <div class="flex justify-between"><span>ব্যাচের সদস্য (স্ত্রী ও সন্তান সহ)</span><span class="font-bold">৳ 2,002</span></div>
        <div class="flex justify-between text-white/50"><span>+ গেস্ট (জনপ্রতি)</span><span>৳ 1,000</span></div>
        <div class="flex justify-between text-white/50"><span>+ ড্রাইভার</span><span>৳ 500</span></div>
        <div class="flex justify-between text-white/50"><span>+ Donation</span><span id="donationDisplay">৳ 0</span></div>
      </div>
      <div class="mt-3 pt-3 border-t border-white/10 grid grid-cols-2 gap-2 text-xs">
        <div class="flex justify-between"><span class="text-white/50">Children: <span id="childCountDisplay" class="text-white font-bold">{{ count($childrenDetails) }}</span></span><span class="text-white/70" id="childSubtotal">{{ count($childrenDetails) > 0 ? 'Included' : '+ ৳ 0' }}</span></div>
        <div class="flex justify-between"><span class="text-white/50">Guests: <span id="guestCountDisplay" class="text-white font-bold">{{ count($guestsDetails) }}</span></span><span class="text-white/70" id="guestSubtotal">+ ৳ {{ count($guestsDetails) * 1000 }}</span></div>
      </div>
      <div class="mt-4 pt-3 border-t border-white/20 flex justify-between items-baseline">
        <div>
          <span class="text-xs text-white/50 uppercase tracking-wide font-semibold">Total Payable</span>
          <p class="text-xs text-white/40 mt-0.5" id="feeBreakdown">ব্যাচের সদস্য (৳2,002)</p>
        </div>
        <span class="text-2xl font-bold text-kgreen" id="grandTotal">৳ {{ number_format($reg ? $reg->total_amount : 2002, 0) }}</span>
      </div>
    </div>

    {{-- Submit --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
      <button type="submit" class="w-full bg-primary text-white font-semibold py-3 rounded-xl hover:opacity-90 transition text-base flex items-center justify-center gap-2">
        <i class="fa-solid fa-floppy-disk"></i>
        {{ $reg ? 'Update Registration' : 'Save Registration' }} &amp; Proceed to Payment →
      </button>
    </div>

  </form>

  {{-- Payment Section (shown after registration exists) --}}
  @if($reg)
  @php
    $paidAmount  = $reg->paid_amount ?? 0;
    $balanceDue  = max(0, $reg->total_amount - $paidAmount);
    $fullyPaid   = $paidAmount >= $reg->total_amount && $reg->payment_status === 'paid';
    $hasPending  = $reg->payment_status === 'pending';
    $needsMore   = $balanceDue > 0 && !$hasPending;
  @endphp
  <div class="mt-5">
    @if($fullyPaid)
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-xl p-5 flex items-center gap-3">
      <span class="text-2xl">✅</span>
      <div>
        <p class="font-semibold text-green-800 dark:text-green-200 text-sm">Payment Confirmed — ৳{{ number_format($reg->total_amount, 0) }}</p>
        <p class="text-green-700 dark:text-green-300 text-xs">Your spot at the reunion is secured. Method: {{ strtoupper($reg->payment_method ?? 'SSLCommerz') }}</p>
      </div>
    </div>
    @elseif($hasPending)
    <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-xl p-5">
      <p class="font-semibold text-blue-800 dark:text-blue-200 text-sm mb-1">⏳ Payment Pending Verification</p>
      <p class="text-blue-700 dark:text-blue-300 text-xs">Reference: <strong>{{ $reg->payment_reference }}</strong> via {{ strtoupper($reg->payment_method ?? '—') }}. Admin will confirm shortly.</p>
      @if($paidAmount > 0)
      <p class="text-blue-600 dark:text-blue-400 text-xs mt-1.5">Previously confirmed: <strong>৳{{ number_format($paidAmount) }}</strong> · Balance being verified: <strong>৳{{ number_format($balanceDue) }}</strong></p>
      @endif
    </div>
    @elseif($needsMore)
    {{-- Additional payment required (guests added after initial payment) --}}
    @if($paidAmount > 0)
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4 mb-4 flex items-start gap-3">
      <span class="text-xl mt-0.5">⚠️</span>
      <div>
        <p class="font-semibold text-amber-800 dark:text-amber-200 text-sm">Additional Payment Required</p>
        <p class="text-amber-700 dark:text-amber-300 text-xs mt-0.5">
          Already paid: <strong>৳{{ number_format($paidAmount) }}</strong> ·
          New total: <strong>৳{{ number_format($reg->total_amount) }}</strong> ·
          Balance due: <strong>৳{{ number_format($balanceDue) }}</strong>
        </p>
      </div>
    </div>
    @endif
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
      <div>
        <p class="font-semibold text-kgray dark:text-gray-200 text-sm mb-0.5">
          💳 {{ $paidAmount > 0 ? 'Additional Payment' : 'Payment Required' }} — ৳{{ number_format($balanceDue, 0) }}
        </p>
        <p class="text-gray-400 dark:text-gray-500 text-xs">Choose your preferred payment method below.</p>
      </div>

      {{-- Method Tabs (grid wraps on mobile) --}}
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        <button type="button" class="pay-tab py-2.5 rounded-xl border-2 border-primary bg-red-50 dark:bg-red-900/20 text-center transition" data-tab="bkash">
          <span class="font-extrabold text-sm block" style="color:#E2136E">bKash</span><span class="text-[10px] text-gray-400 dark:text-gray-500">Send Money</span>
        </button>
        <button type="button" class="pay-tab py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 text-center hover:border-gray-300 dark:hover:border-gray-500 transition" data-tab="nagad">
          <span class="font-extrabold text-sm block" style="color:#F7931E">Nagad</span><span class="text-[10px] text-gray-400 dark:text-gray-500">Send Money</span>
        </button>
        <button type="button" class="pay-tab py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 text-center hover:border-gray-300 dark:hover:border-gray-500 transition" data-tab="bank">
          <span class="font-extrabold text-sm block" style="color:#0F52BA">Bank</span><span class="text-[10px] text-gray-400 dark:text-gray-500">Deposit</span>
        </button>
        <a href="{{ route('payment.confirm') }}" class="py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 text-center hover:border-kgreen transition block">
          <span class="font-extrabold text-sm block text-kgreen">Online</span><span class="text-[10px] text-gray-400 dark:text-gray-500">SSL / Card</span>
        </a>
      </div>

      {{-- bKash --}}
      <div id="tab-bkash" class="pay-panel bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600 space-y-3">
        <div class="text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700 space-y-1.5">
          <p>1. Open <strong>bKash App</strong> or dial <strong>*247#</strong></p>
          <p>2. Select <strong>"Send Money"</strong> → <strong class="font-mono" style="color:#E2136E">+880 1711-740273</strong></p>
          <p>3. Amount: <strong class="text-primary">৳ {{ number_format($balanceDue, 0) }}</strong></p>
          <p>4. Reference: your <strong>name or phone number</strong></p>
        </div>
        <form method="POST" action="{{ route('payment.manual') }}" class="space-y-3">
          @csrf
          <input type="hidden" name="payment_method" value="bkash">
          <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Sender bKash Mobile <span class="text-brand">*</span></label>
            <input type="tel" name="sender_number" placeholder="01XXXXXXXXX" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen"></div>
          <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">bKash Transaction ID <span class="text-brand">*</span></label>
            <input type="text" name="payment_reference" required placeholder="e.g. BL78X90A1" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-kgreen"></div>
          <button type="submit" class="w-full py-2.5 rounded-xl text-white font-bold text-sm hover:opacity-90 transition" style="background:#E2136E">Verify &amp; Confirm bKash Payment</button>
        </form>
      </div>

      {{-- Nagad --}}
      <div id="tab-nagad" class="pay-panel hidden bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600 space-y-3">
        <div class="text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700 space-y-1.5">
          <p>1. Open <strong>Nagad App</strong> or dial <strong>*167#</strong></p>
          <p>2. Select <strong>"Send Money"</strong> → <strong class="font-mono" style="color:#F7931E">01912-345678</strong></p>
          <p>3. Amount: <strong class="text-primary">৳ {{ number_format($balanceDue, 0) }}</strong></p>
        </div>
        <form method="POST" action="{{ route('payment.manual') }}" class="space-y-3">
          @csrf
          <input type="hidden" name="payment_method" value="nagad">
          <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Sender Nagad Mobile <span class="text-brand">*</span></label>
            <input type="tel" name="sender_number" placeholder="01XXXXXXXXX" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen"></div>
          <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Nagad Transaction ID <span class="text-brand">*</span></label>
            <input type="text" name="payment_reference" required placeholder="e.g. 71A89KC01" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-kgreen"></div>
          <button type="submit" class="w-full py-2.5 rounded-xl text-white font-bold text-sm hover:opacity-90 transition" style="background:#F7931E">Verify &amp; Confirm Nagad Payment</button>
        </form>
      </div>

      {{-- Bank --}}
      <div id="tab-bank" class="pay-panel hidden bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600 space-y-3">
        <div class="text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700 space-y-1.5">
          <p><strong>Bank:</strong> Dutch-Bangla Bank PLC / City Bank PLC</p>
          <p><strong>Account Name:</strong> KZS BATCH 2002 REUNION FUND</p>
          <p><strong>Account No.:</strong> 151.110.0098765</p>
          <p><strong>Branch:</strong> Kushtia Branch (Routing: 090500123)</p>
        </div>
        <form method="POST" action="{{ route('payment.manual') }}" class="space-y-3">
          @csrf
          <input type="hidden" name="payment_method" value="bank_transfer">
          <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Depositor Bank &amp; Branch <span class="text-brand">*</span></label>
            <input type="text" name="sender_number" placeholder="e.g. DBBL Kushtia / Online NPSB" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen"></div>
          <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Deposit Slip / Transfer Ref <span class="text-brand">*</span></label>
            <input type="text" name="payment_reference" required placeholder="e.g. FT26092500891" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-kgreen"></div>
          <button type="submit" class="w-full py-2.5 rounded-xl text-white font-bold text-sm hover:opacity-90 transition" style="background:#0F52BA">Submit Bank Verification</button>
        </form>
      </div>
    </div>
    @endif
  </div>
  @endif

</div>

<script>
  // ── Photo preview ─────────────────────────────────────────────
  function previewUpload(input, previewId, iconId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      let img = document.getElementById(previewId);
      if (!img) {
        img = document.createElement('img');
        img.id = previewId;
        img.className = 'w-24 h-28 mx-auto object-cover rounded-xl border-2 border-gray-300 dark:border-gray-600 shadow mb-3';
        const icon = document.getElementById(iconId);
        if (icon) icon.replaceWith(img);
        else input.parentElement.prepend(img);
      }
      img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }

  // ── Dynamic children ─────────────────────────────────────────
  let childIdx = {{ count($childrenDetails) }};
  document.getElementById('btnAddChild').addEventListener('click', () => {
    const i = childIdx++;
    const card = document.createElement('div');
    card.className = 'child-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-3 space-y-2.5';
    card.innerHTML = `
      <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
        <span class="text-xs font-bold text-kgray dark:text-gray-300 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-child text-primary"></i> Child Details</span>
        <button type="button" class="remove-child text-xs font-semibold text-red-500 hover:text-red-700 flex items-center gap-1"><i class="fa-solid fa-trash-can"></i> Remove</button>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
        <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Name (English) <span class="text-brand">*</span></label>
          <input type="text" name="children_details[${i}][name]" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="e.g. Abrar Hasan"></div>
        <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">নাম (বাংলায়)</label>
          <input type="text" name="children_details[${i}][name_bn]" style="font-family:'Tiro Bangla',serif" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="যেমন: আবরার হাসান"></div>
      </div>`;
    card.querySelector('.remove-child').addEventListener('click', () => { card.remove(); calcFees(); });
    document.getElementById('childrenContainer').appendChild(card);
    document.getElementById('emptyChildrenNotice').classList.add('hidden');
    calcFees();
  });

  // Wire pre-filled remove buttons
  document.querySelectorAll('.remove-child').forEach(btn => {
    btn.addEventListener('click', () => { btn.closest('.child-card').remove(); calcFees(); });
  });

  // ── Dynamic guests ────────────────────────────────────────────
  let guestIdx = {{ count($guestsDetails) }};
  document.getElementById('btnAddGuest').addEventListener('click', () => {
    const i = guestIdx++;
    const card = document.createElement('div');
    card.className = 'guest-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-3 space-y-2.5';
    card.innerHTML = `
      <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
        <span class="text-xs font-bold text-kgray dark:text-gray-300 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-user-plus text-primary"></i> Guest Details</span>
        <button type="button" class="remove-guest text-xs font-semibold text-red-500 hover:text-red-700 flex items-center gap-1"><i class="fa-solid fa-trash-can"></i> Remove</button>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
        <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Full Name <span class="text-brand">*</span></label>
          <input type="text" name="guests_details[${i}][name]" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="Full name"></div>
        <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Relationship</label>
          <input type="text" name="guests_details[${i}][relationship]" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="e.g. Brother / Cousin"></div>
        <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Contact Number</label>
          <input type="tel" name="guests_details[${i}][contact]" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-kgreen" placeholder="+880 1XXXXXXXXX"></div>
      </div>`;
    card.querySelector('.remove-guest').addEventListener('click', () => { card.remove(); calcFees(); });
    document.getElementById('guestsContainer').appendChild(card);
    document.getElementById('emptyGuestsNotice').classList.add('hidden');
    calcFees();
  });

  document.querySelectorAll('.remove-guest').forEach(btn => {
    btn.addEventListener('click', () => { btn.closest('.guest-card').remove(); calcFees(); });
  });

  // ── Same-address toggle ───────────────────────────────────────
  const presIds = ['pres_road','pres_post','pres_thana','pres_postcode','pres_district'];
  const permIds = ['perm_road','perm_post','perm_thana','perm_postcode','perm_district'];
  document.getElementById('sameAddr').addEventListener('change', function() {
    if (this.checked) {
      permIds.forEach((id, i) => { document.getElementById(id).value = document.getElementById(presIds[i]).value; document.getElementById(id).readOnly = true; });
    } else {
      permIds.forEach(id => { document.getElementById(id).readOnly = false; });
    }
  });
  presIds.forEach(id => document.getElementById(id).addEventListener('input', () => {
    if (document.getElementById('sameAddr').checked) {
      permIds.forEach((pid, i) => document.getElementById(pid).value = document.getElementById(presIds[i]).value);
    }
  }));

  // ── Live fee calculator ───────────────────────────────────────
  function calcFees() {
    const spouse   = document.getElementById('bring_spouse').checked;
    const children = document.querySelectorAll('.child-card').length;
    const guests   = document.querySelectorAll('.guest-card').length;
    const driver   = document.getElementById('driver_included').checked;
    const donation = parseInt(document.getElementById('donation_amount').value) || 0;

    // Base 2002 covers member + spouse + all children
    const total = 2002 + (guests * 1000) + (driver ? 500 : 0) + donation;

    document.getElementById('childCountDisplay').textContent  = children;
    document.getElementById('childSubtotal').textContent     = children > 0 ? 'Included' : '+ ৳ 0';
    document.getElementById('guestCountDisplay').textContent  = guests;
    document.getElementById('guestSubtotal').textContent     = '+ ৳ ' + (guests * 1000).toLocaleString();
    document.getElementById('donationDisplay').textContent   = '৳ ' + donation.toLocaleString();
    document.getElementById('grandTotal').textContent        = '৳ ' + total.toLocaleString();

    const parts = ['ব্যাচের সদস্য (৳2,002)'];
    if (guests)    parts.push(`${guests} Guest(s) (৳${(guests*1000).toLocaleString()})`);
    if (driver)    parts.push('Driver (৳500)');
    if (donation)  parts.push(`Donation (৳${donation.toLocaleString()})`);
    document.getElementById('feeBreakdown').textContent = parts.join(' + ');

    document.getElementById('emptyChildrenNotice').classList.toggle('hidden', children > 0);
    document.getElementById('emptyGuestsNotice').classList.toggle('hidden', guests > 0);
  }

  function setDonation(amount) {
    document.getElementById('donation_amount').value = amount;
    calcFees();
  }

  calcFees();

  // ── Payment method tabs ───────────────────────────────────────
  document.querySelectorAll('.pay-tab').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.pay-tab').forEach(b => b.className = 'pay-tab py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 text-center hover:border-gray-300 dark:hover:border-gray-500 transition');
      this.className = 'pay-tab py-2.5 rounded-xl border-2 border-primary bg-red-50 dark:bg-red-900/20 text-center transition';
      document.querySelectorAll('.pay-panel').forEach(p => p.classList.add('hidden'));
      document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
    });
  });
</script>
@endsection
