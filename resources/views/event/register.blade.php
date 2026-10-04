@extends('layouts.app')
@section('title', 'ইভেন্ট রেজিস্ট্রেশন — KZS 2002')

@section('content')
@php
  $reg = $registration;
  $childrenDetails = $reg?->children_details ?? [];
  $guestsDetails   = $reg?->guests_details ?? [];
@endphp

<div class="m-main">
<div class="wrap" style="max-width:860px;margin-inline:auto">

  {{-- Banner --}}
  <div style="border-radius:12px;overflow:hidden;margin-bottom:20px;box-shadow:var(--shadow)">
    <img src="{{ asset('images/cover.jpg') }}" alt="KZS 2002 Silver Jubilee Reunion" style="width:100%;object-fit:cover;display:block">
  </div>

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
    <div>
      <h1 class="m-title" style="font-size:22px" data-en="Silver Jubilee Event Registration">সিলভার জুবিলি ইভেন্ট রেজিস্ট্রেশন</h1>
      <p style="color:var(--muted);font-size:13px" data-en="KZS 2002 SSC Batch — Celebrating 25 Years of Unity">KZS ২০০২ SSC ব্যাচ — ঐক্যের ২৫ বছর উদযাপন</p>
    </div>
    <a href="{{ route('dashboard') }}" style="font-size:13px;color:var(--muted);text-decoration:none" data-en="← Dashboard">← ড্যাশবোর্ড</a>
  </div>

  @if($reg)
  <div style="background:#f0fdf4;border:1px solid #86efac;color:#166534;font-size:13px;border-radius:10px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:8px">
    <i class="fa-solid fa-circle-check"></i>
    <span data-en="You are already registered. Update your details below.">আপনি ইতিমধ্যে রেজিস্ট্রেশন করেছেন। নিচে আপডেট করতে পারবেন।</span>
    @if($reg->payment_status === 'paid')
      <span style="margin-left:auto;font-weight:700;color:#14532d">✅ পেমেন্ট নিশ্চিত — ৳{{ number_format($reg->total_amount, 0) }}</span>
    @endif
  </div>
  @endif

  <form method="POST" action="{{ route('event.save') }}" enctype="multipart/form-data" style="display:grid;gap:16px" id="regForm">
    @csrf

    {{-- SECTION 1: Schooling --}}
    <div class="panel">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--line)">
        <div style="width:32px;height:32px;border-radius:8px;background:var(--tint);color:var(--red-700);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="fa-solid fa-school" style="font-size:13px"></i>
        </div>
        <div>
          <h2 style="font-size:12px;font-weight:700;color:var(--ink);text-transform:uppercase;letter-spacing:.06em" data-en="1. Schooling Information at KZS">১. KZS-এ স্কুলের তথ্য</h2>
          <p style="font-size:11px;color:var(--muted)" data-en="Your class and shift records at Kushtia Zilla School Batch '02">কুষ্টিয়া জিলা স্কুল ব্যাচ '০২-এ আপনার শ্রেণি ও শিফট</p>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px">
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Studied Up To *">কত ক্লাস পর্যন্ত <span style="color:var(--red-700)">*</span></label>
          <select name="school_class" required style="@error('school_class') border-color:var(--red-700); @enderror">
            <option value="" disabled {{ !old('school_class', $alumni->school_class) ? 'selected' : '' }} data-en="Select Class">ক্লাস বেছে নিন</option>
            @foreach(['Class 10 (Completed SSC 2002)','Class 9','Class 8','Class 7','Class 6','Class 5','Class 4','Class 3'] as $cls)
              <option value="{{ $cls }}" {{ old('school_class', $alumni->school_class) === $cls ? 'selected' : '' }}>{{ $cls }}</option>
            @endforeach
          </select>
          @error('school_class') <p style="color:var(--red-700);font-size:11px;margin-top:3px">{{ $message }}</p> @enderror
        </div>
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="School Shift *">শিফট <span style="color:var(--red-700)">*</span></label>
          <div style="display:flex;gap:8px">
            @foreach(['Morning' => 'Morning', 'Day' => 'Day'] as $val => $label)
            <label class="shift-opt" style="flex:1;cursor:pointer">
              <input type="radio" name="school_shift" value="{{ $val }}" style="display:none"
                {{ old('school_shift', $alumni->school_shift) === $val ? 'checked' : '' }} required>
              <div class="shift-face" style="text-align:center;border:2px solid var(--line);border-radius:8px;padding:8px 4px;font-size:13px;font-weight:600;color:var(--muted);user-select:none;transition:.15s">{{ $label }}</div>
            </label>
            @endforeach
          </div>
          @error('school_shift') <p style="color:var(--red-700);font-size:11px;margin-top:3px">{{ $message }}</p> @enderror
        </div>
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Section *">সেকশন <span style="color:var(--red-700)">*</span></label>
          <select name="school_section" required>
            <option value="B" {{ old('school_section', $alumni->section) !== 'A' ? 'selected' : '' }}>Section B (Batch Main)</option>
            <option value="A" {{ old('school_section', $alumni->section) === 'A' ? 'selected' : '' }}>Section A</option>
          </select>
        </div>
      </div>
    </div>

    {{-- SECTION 2: Personal & Professional --}}
    <div class="panel">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--line)">
        <div style="width:32px;height:32px;border-radius:8px;background:var(--tint);color:var(--red-700);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="fa-solid fa-user-graduate" style="font-size:13px"></i>
        </div>
        <div>
          <h2 style="font-size:12px;font-weight:700;color:var(--ink);text-transform:uppercase;letter-spacing:.06em" data-en="2. Personal & Professional Info">২. ব্যক্তিগত ও পেশাগত তথ্য</h2>
          <p style="font-size:11px;color:var(--muted)" data-en="Identity, bilingual name, T-shirt size, and professional details">পরিচিতি, দ্বিভাষিক নাম, টি-শার্ট সাইজ ও পেশার বিবরণ</p>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px">
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Full Name (English)">পুরো নাম (ইংরেজিতে)</label>
          <input type="text" value="{{ $alumni->name }}" disabled style="background:var(--tint);color:var(--muted);cursor:not-allowed">
          <p style="font-size:11px;color:var(--muted);margin-top:3px" data-en="Edit via Profile page">
            <a href="{{ route('profile.edit') }}" style="color:var(--red-700)" data-en="Profile page">প্রোফাইল পেজ</a> থেকে সম্পাদনা করুন
          </p>
        </div>
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px">সম্পূর্ণ নাম (বাংলায়) <span style="color:var(--muted);font-size:11px;font-weight:400">(ঐচ্ছিক)</span></label>
          <input type="text" name="name_bn" value="{{ old('name_bn', $alumni->name_bn) }}"
            placeholder="যেমন: মো: ইমরুল হাসান" style="font-family:var(--f-body);@error('name_bn') border-color:var(--red-700); @enderror">
          @error('name_bn') <p style="color:var(--red-700);font-size:11px;margin-top:3px">{{ $message }}</p> @enderror
        </div>
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px">
            <i class="fa-brands fa-whatsapp" style="color:#16a34a"></i> Mobile &amp; WhatsApp <span style="color:var(--red-700)">*</span>
          </label>
          <input type="tel" name="mobile" required value="{{ old('mobile', $alumni->mobile ?? $alumni->phone) }}"
            placeholder="+880 17XXXXXXXX" style="@error('mobile') border-color:var(--red-700); @enderror">
          @error('mobile') <p style="color:var(--red-700);font-size:11px;margin-top:3px">{{ $message }}</p> @enderror
        </div>
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px">
            <i class="fa-solid fa-phone-volume" style="color:var(--red-700)"></i> Emergency Contact <span style="color:var(--red-700)">*</span>
          </label>
          <input type="tel" name="emergency_contact" required value="{{ old('emergency_contact', $alumni->emergency_contact) }}"
            placeholder="+880 18XXXXXXXX" style="@error('emergency_contact') border-color:var(--red-700); @enderror">
          @error('emergency_contact') <p style="color:var(--red-700);font-size:11px;margin-top:3px">{{ $message }}</p> @enderror
        </div>
        <div style="grid-column:1/-1">
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Highest Educational Qualification *">সর্বোচ্চ শিক্ষাগত যোগ্যতা <span style="color:var(--red-700)">*</span></label>
          <input type="text" name="higher_education" required value="{{ old('higher_education', $alumni->higher_education) }}"
            placeholder="e.g. B.Sc in EEE (BUET) / MBA (DU) / MBBS"
            style="@error('higher_education') border-color:var(--red-700); @enderror">
          @error('higher_education') <p style="color:var(--red-700);font-size:11px;margin-top:3px">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- T-Shirt Size --}}
      <div style="margin-bottom:20px;border:1px solid var(--line);border-radius:10px;overflow:hidden">
        <div style="display:flex;flex-wrap:wrap">
          <div style="width:200px;flex-shrink:0;background:var(--tint);display:flex;align-items:center;justify-content:center;padding:16px;border-right:1px solid var(--line)">
            <img src="{{ asset('images/t-shirt.jpg') }}" alt="KZS 2002 Reunion T-Shirt" style="max-height:180px;width:auto;object-fit:contain">
          </div>
          <div style="flex:1;padding:16px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
              <p style="font-size:13px;font-weight:600;color:var(--ink)">
                <i class="fa-solid fa-shirt" style="color:var(--red-700);margin-right:6px"></i>
                <span data-en="Reunion T-Shirt Size *">রিইউনিয়ন টি-শার্ট সাইজ</span> <span style="color:var(--red-700)">*</span>
              </p>
              <span style="font-size:10px;color:var(--muted);background:var(--tint);padding:2px 8px;border-radius:99px" data-en="Chest inches">বুকের ইঞ্চি</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px">
              @foreach(['S' => '36"-38"','M' => '38"-40"','L' => '40"-42"','XL' => '42"-44"','XXL' => '44"-46"','XXXL' => '46"-48"'] as $size => $range)
              <label class="size-opt" style="cursor:pointer" onclick="selectSize(this,'{{ $size }}')">
                <input type="radio" name="tshirt_size" value="{{ $size }}" style="display:none"
                  {{ old('tshirt_size', $reg?->tshirt_size ?? 'M') === $size ? 'checked' : '' }}>
                <div class="size-face" style="border:2px solid var(--line);border-radius:10px;padding:10px 4px;text-align:center;user-select:none;transition:.15s">
                  <span style="font-size:13px;font-weight:700;display:block;color:var(--ink)">{{ $size }}</span>
                  <span style="font-size:10px;display:block;color:var(--muted);margin-top:2px">{{ $range }}</span>
                </div>
              </label>
              @endforeach
            </div>
            @error('tshirt_size') <p style="color:var(--red-700);font-size:11px;margin-top:8px">{{ $message }}</p> @enderror
            <p style="font-size:11px;color:var(--muted);margin-top:10px" data-en="Sizes are based on chest measurement. If between sizes, choose the larger one.">
              <i class="fa fa-circle-info" style="margin-right:4px"></i>সাইজ বুকের মাপে। দুটির মাঝামাঝি হলে বড়টি বেছে নিন।
            </p>
          </div>
        </div>
      </div>

      {{-- Professional --}}
      <div style="background:var(--tint);border-radius:8px;padding:14px">
        <p style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;display:flex;align-items:center;gap:6px" data-en="Professional Information">
          <i class="fa-solid fa-briefcase"></i> পেশাগত তথ্য
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
          <div>
            <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Designation / Title *">পদবি <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="designation" required value="{{ old('designation', $alumni->designation) }}"
              placeholder="e.g. Senior Software Engineer / Manager"
              style="@error('designation') border-color:var(--red-700); @enderror">
            @error('designation') <p style="color:var(--red-700);font-size:11px;margin-top:3px">{{ $message }}</p> @enderror
          </div>
          <div>
            <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Organization *">প্রতিষ্ঠান <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="organization" required value="{{ old('organization', $alumni->organization) }}"
              placeholder="e.g. Grameenphone / Tech Solutions Ltd."
              style="@error('organization') border-color:var(--red-700); @enderror">
            @error('organization') <p style="color:var(--red-700);font-size:11px;margin-top:3px">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- SECTION 3: Family & Guests --}}
    <div class="panel">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--line)">
        <div style="width:32px;height:32px;border-radius:8px;background:var(--tint);color:var(--red-700);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="fa-solid fa-people-roof" style="font-size:13px"></i>
        </div>
        <div>
          <h2 style="font-size:12px;font-weight:700;color:var(--ink);text-transform:uppercase;letter-spacing:.06em" data-en="3. Family & Accompanying Persons">৩. পরিবার ও সঙ্গীগণ</h2>
          <p style="font-size:11px;color:var(--muted)">ব্যাচের সদস্য (স্ত্রী ও সন্তান সহ) — ৳২,০০২ · গেস্ট (জনপ্রতি) — ৳১,০০০ · ড্রাইভার — ৳৫০০</p>
        </div>
      </div>

      <div style="display:grid;gap:16px">
        {{-- Spouse --}}
        <div style="background:var(--tint);border-radius:8px;padding:14px">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:12px">
            <input type="checkbox" name="bring_spouse" id="bring_spouse" value="1" style="width:16px;height:16px;accent-color:var(--red-700)"
              onchange="calcFees()" {{ old('bring_spouse', $reg?->bring_spouse) ? 'checked' : '' }}>
            <span style="font-size:13px;font-weight:500;color:var(--ink)">
              <i class="fa-solid fa-heart" style="color:var(--red-700);margin-right:4px"></i>
              <span data-en="Spouse Attending">স্ত্রী/স্বামী আসবেন</span>
              <span style="color:var(--muted);font-weight:400;font-size:12px" data-en="(included in base fee)">(বেস ফি-তে অন্তর্ভুক্ত)</span>
            </span>
          </label>
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px">
            <div>
              <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Spouse Name (English)">স্ত্রী/স্বামীর নাম (ইংরেজি)</label>
              <input type="text" name="spouse_name" value="{{ old('spouse_name', $reg?->spouse_name ?? $alumni->spouse_name) }}" placeholder="Spouse full name">
            </div>
            <div>
              <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px">স্ত্রীর নাম (বাংলায়)</label>
              <input type="text" name="spouse_name_bn" value="{{ old('spouse_name_bn', $reg?->spouse_name_bn) }}"
                placeholder="স্ত্রীর সম্পূর্ণ নাম" style="font-family:var(--f-body)">
            </div>
          </div>
        </div>

        {{-- Children --}}
        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <p style="font-size:13px;font-weight:500;color:var(--ink)">
              <i class="fa-solid fa-child" style="color:var(--red-700);margin-right:4px"></i>
              <span data-en="Children Attending">সন্তান আসবে</span>
              <span style="color:var(--muted);font-size:12px;font-weight:400" data-en="(included in base fee)">(বেস ফি-তে অন্তর্ভুক্ত)</span>
            </p>
            <button type="button" id="btnAddChild" class="btn btn-ghost btn-sm">
              <i class="fa-solid fa-plus"></i> <span data-en="Add Child">সন্তান যোগ করুন</span>
            </button>
          </div>
          <div id="childrenContainer" style="display:grid;gap:10px">
            @foreach($childrenDetails as $i => $child)
            <div class="child-card" style="border:1px solid var(--line);border-radius:8px;padding:12px">
              <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line);padding-bottom:8px;margin-bottom:10px">
                <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">
                  <i class="fa-solid fa-child" style="color:var(--red-700)"></i> <span data-en="Child Details">সন্তানের বিবরণ</span>
                </span>
                <button type="button" class="remove-child" style="font-size:12px;color:var(--red-700);background:none;border:none;cursor:pointer;font-weight:600">
                  <i class="fa-solid fa-trash-can"></i> <span data-en="Remove">সরান</span>
                </button>
              </div>
              <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px">
                <div>
                  <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Name (English) *">নাম (ইংরেজি) <span style="color:var(--red-700)">*</span></label>
                  <input type="text" name="children_details[{{ $i }}][name]" value="{{ $child['name'] ?? '' }}" required placeholder="e.g. Abrar Hasan">
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px">নাম (বাংলায়)</label>
                  <input type="text" name="children_details[{{ $i }}][name_bn]" value="{{ $child['name_bn'] ?? '' }}" style="font-family:var(--f-body)" placeholder="যেমন: আবরার হাসান">
                </div>
              </div>
            </div>
            @endforeach
          </div>
          <div id="emptyChildrenNotice" style="{{ count($childrenDetails) > 0 ? 'display:none' : '' }}text-align:center;padding:14px;font-size:12px;color:var(--muted);background:var(--tint);border-radius:8px;border:1px dashed var(--line);margin-top:8px">
            <span data-en="No children added. Click + Add Child if bringing kids.">কোনো সন্তান যোগ হয়নি। যোগ করতে <strong>+ সন্তান যোগ করুন</strong> ক্লিক করুন।</span>
          </div>
        </div>

        {{-- Guests --}}
        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <p style="font-size:13px;font-weight:500;color:var(--ink)">
              <i class="fa-solid fa-users" style="color:var(--red-700);margin-right:4px"></i>
              <span data-en="Additional Guests">অতিরিক্ত গেস্ট</span>
              <span style="color:var(--muted);font-size:12px;font-weight:400">(+৳১,০০০ জনপ্রতি)</span>
            </p>
            <button type="button" id="btnAddGuest" class="btn btn-ghost btn-sm">
              <i class="fa-solid fa-user-plus"></i> <span data-en="Add Guest">গেস্ট যোগ করুন</span>
            </button>
          </div>
          <div id="guestsContainer" style="display:grid;gap:10px">
            @foreach($guestsDetails as $i => $guest)
            <div class="guest-card" style="border:1px solid var(--line);border-radius:8px;padding:12px">
              <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line);padding-bottom:8px;margin-bottom:10px">
                <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">
                  <i class="fa-solid fa-user-plus" style="color:var(--red-700)"></i> <span data-en="Guest Details">গেস্টের বিবরণ</span>
                </span>
                <button type="button" class="remove-guest" style="font-size:12px;color:var(--red-700);background:none;border:none;cursor:pointer;font-weight:600">
                  <i class="fa-solid fa-trash-can"></i> <span data-en="Remove">সরান</span>
                </button>
              </div>
              <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px">
                <div>
                  <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Full Name *">পুরো নাম <span style="color:var(--red-700)">*</span></label>
                  <input type="text" name="guests_details[{{ $i }}][name]" value="{{ $guest['name'] ?? '' }}" required placeholder="Full name">
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Relationship">সম্পর্ক</label>
                  <input type="text" name="guests_details[{{ $i }}][relationship]" value="{{ $guest['relationship'] ?? '' }}" placeholder="e.g. Brother">
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Contact">যোগাযোগ</label>
                  <input type="tel" name="guests_details[{{ $i }}][contact]" value="{{ $guest['contact'] ?? '' }}" placeholder="+880 1XXXXXXXXX">
                </div>
              </div>
            </div>
            @endforeach
          </div>
          <div id="emptyGuestsNotice" style="{{ count($guestsDetails) > 0 ? 'display:none' : '' }}text-align:center;padding:14px;font-size:12px;color:var(--muted);background:var(--tint);border-radius:8px;border:1px dashed var(--line);margin-top:8px">
            <span data-en="No guests added.">কোনো গেস্ট যোগ হয়নি। <strong>+ গেস্ট যোগ করুন</strong> ক্লিক করুন।</span>
          </div>
        </div>

        {{-- Driver --}}
        <div style="background:var(--tint);border-radius:8px;padding:12px">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
            <input type="checkbox" name="driver_included" id="driver_included" value="1"
              style="width:16px;height:16px;accent-color:var(--red-700)"
              onchange="calcFees()" {{ old('driver_included', $reg?->driver_included) ? 'checked' : '' }}>
            <span style="font-size:13px;font-weight:500;color:var(--ink)">
              <i class="fa-solid fa-car" style="color:var(--muted);margin-right:4px"></i>
              <span data-en="Driver Entry & Meal Pack">ড্রাইভার প্রবেশ ও খাবার প্যাক</span>
              <span style="color:var(--muted);font-weight:400">(+৳৫০০)</span>
            </span>
          </label>
        </div>

        {{-- Donation --}}
        <div style="border:1px dashed #86efac;border-radius:10px;padding:16px;background:#f0fdf4">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
            <i class="fa-solid fa-hand-holding-heart" style="color:#16a34a"></i>
            <span style="font-size:13px;font-weight:600;color:var(--ink)" data-en="Donation">ডোনেশন</span>
          </div>
          <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:14px;font-weight:700;color:var(--muted)">৳</span>
            <input type="text" name="donation_amount" id="donation_amount"
              value="{{ old('donation_amount', $reg?->donation_amount > 0 ? $reg->donation_amount : '') }}"
              oninput="calcFees()" placeholder="0" style="flex:1">
          </div>
        </div>
      </div>
    </div>

    {{-- SECTION 4: Address --}}
    <div class="panel">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--line)">
        <div style="width:32px;height:32px;border-radius:8px;background:var(--tint);color:var(--red-700);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="fa-solid fa-map-location-dot" style="font-size:13px"></i>
        </div>
        <div>
          <h2 style="font-size:12px;font-weight:700;color:var(--ink);text-transform:uppercase;letter-spacing:.06em" data-en="4. Present & Permanent Address">৪. বর্তমান ও স্থায়ী ঠিকানা</h2>
          <p style="font-size:11px;color:var(--muted)" data-en="Complete address with Thana, Upazilla, and District">থানা, উপজেলা ও জেলাসহ সম্পূর্ণ ঠিকানা</p>
        </div>
      </div>

      @php
        function parseAddr(string $addr): array {
          $parts = ['road'=>'','post'=>'','thana'=>'','postcode'=>'','district'=>''];
          if (preg_match('/^(.*?),\s*PO:\s*(.*?),\s*(.*?),\s*(?:PC:|Upazilla?:)\s*(.*?),\s*(.*)$/', $addr, $m)) {
            $parts = ['road'=>trim($m[1]),'post'=>trim($m[2]),'thana'=>trim($m[3]),'postcode'=>trim($m[4]),'district'=>trim($m[5])];
          }
          return $parts;
        }
        $pres = parseAddr($alumni->present_address ?? '');
        $perm = parseAddr($alumni->permanent_address ?? '');
      @endphp

      <div style="margin-bottom:20px">
        <p style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px" data-en="Present Address">বর্তমান ঠিকানা</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px">
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Village / Road / House *">গ্রাম / রাস্তা / বাড়ি <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="pres_road" required value="{{ old('pres_road', $pres['road']) }}" id="pres_road"
              placeholder="House 12, Road 4" style="@error('pres_road') border-color:var(--red-700); @enderror">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Post Office *">পোস্ট অফিস <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="pres_post" required value="{{ old('pres_post', $pres['post']) }}" id="pres_post"
              placeholder="Post Office" style="@error('pres_post') border-color:var(--red-700); @enderror">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Thana *">থানা <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="pres_thana" required value="{{ old('pres_thana', $pres['thana']) }}" id="pres_thana" placeholder="Thana">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Upazilla *">উপজেলা <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="pres_postcode" required value="{{ old('pres_postcode', $pres['postcode']) }}" id="pres_postcode" placeholder="Upazilla">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="District *">জেলা <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="pres_district" required value="{{ old('pres_district', $pres['district']) }}" id="pres_district" placeholder="District">
          </div>
        </div>
      </div>

      <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <p style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em" data-en="Permanent Address">স্থায়ী ঠিকানা</p>
          <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:var(--ink);cursor:pointer;background:var(--tint);padding:5px 10px;border-radius:8px;border:1px solid var(--line)">
            <input type="checkbox" id="sameAddr" style="width:14px;height:14px;accent-color:var(--red-700)">
            <span data-en="Same as Present">বর্তমানের মতো</span>
          </label>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px">
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Village / Road / House *">গ্রাম / রাস্তা / বাড়ি <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="perm_road" required value="{{ old('perm_road', $perm['road']) }}" id="perm_road" placeholder="Village / Road">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Post Office *">পোস্ট অফিস <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="perm_post" required value="{{ old('perm_post', $perm['post']) }}" id="perm_post" placeholder="Post Office">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Thana *">থানা <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="perm_thana" required value="{{ old('perm_thana', $perm['thana']) }}" id="perm_thana" placeholder="Thana">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="Upazilla *">উপজেলা <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="perm_postcode" required value="{{ old('perm_postcode', $perm['postcode']) }}" id="perm_postcode" placeholder="Upazilla">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px" data-en="District *">জেলা <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="perm_district" required value="{{ old('perm_district', $perm['district']) }}" id="perm_district" placeholder="District">
          </div>
        </div>
      </div>
    </div>

    {{-- SECTION 5: Photos --}}
    <div class="panel">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--line)">
        <div style="width:32px;height:32px;border-radius:8px;background:var(--tint);color:var(--red-700);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="fa-solid fa-camera" style="font-size:13px"></i>
        </div>
        <div>
          <h2 style="font-size:12px;font-weight:700;color:var(--ink);text-transform:uppercase;letter-spacing:.06em" data-en="5. Photographs Upload">৫. ছবি আপলোড</h2>
          <p style="font-size:11px;color:var(--muted)" data-en="Passport photo and optional family picture for souvenir album">পাসপোর্ট সাইজ ছবি ও ঐচ্ছিক পারিবারিক ছবি (স্মরণিকার জন্য)</p>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        {{-- Passport --}}
        <div style="border:2px dashed var(--line);border-radius:10px;padding:20px;text-align:center">
          @if($alumni->photo_url)
          @php $photoSrc = str_starts_with($alumni->photo_url, 'uploads/') ? asset($alumni->photo_url) : asset('storage/' . $alumni->photo_url); @endphp
          <div style="margin-bottom:12px"><img src="{{ $photoSrc }}" id="passport_preview" alt="Current Photo" style="width:80px;height:96px;margin:0 auto;object-fit:cover;border-radius:8px;border:2px solid var(--line);display:block"></div>
          @else
          <div id="passport_icon" style="width:40px;height:40px;border-radius:50%;background:var(--tint);color:var(--red-700);display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
            <i class="fa-solid fa-user-tie"></i>
          </div>
          @endif
          <p style="font-size:13px;font-weight:600;color:var(--ink)" data-en="Passport Photo">পাসপোর্ট ছবি
            <span style="color:var(--muted);font-size:11px;font-weight:400">({{ $alumni->photo_url ? 'আপডেট' : 'প্রয়োজনীয়' }})</span>
          </p>
          <p style="font-size:11px;color:var(--muted);margin-top:4px" data-en="Clear headshot · JPG/PNG · max 3MB">পরিষ্কার হেডশট · JPG/PNG · ৩ MB পর্যন্ত</p>
          <input type="file" name="passport_photo" id="passport_photo" accept="image/*" {{ !$alumni->photo_url ? 'required' : '' }}
            style="display:none" onchange="previewUpload(this,'passport_preview','passport_icon')">
          <label for="passport_photo" style="display:inline-block;margin-top:10px;cursor:pointer;padding:6px 12px;border-radius:8px;background:var(--tint);border:1px solid var(--line);font-size:12px;font-weight:600;color:var(--ink)" data-en="Browse Image">ছবি বেছে নিন</label>
        </div>
        {{-- Family --}}
        <div style="border:2px dashed var(--line);border-radius:10px;padding:20px;text-align:center">
          @if($alumni->family_photo_url)
          @php $familySrc = str_starts_with($alumni->family_photo_url, 'uploads/') ? asset($alumni->family_photo_url) : asset('storage/' . $alumni->family_photo_url); @endphp
          <div style="margin-bottom:12px"><img src="{{ $familySrc }}" id="family_preview" alt="Family Photo" style="width:120px;height:96px;margin:0 auto;object-fit:cover;border-radius:8px;border:2px solid var(--line);display:block"></div>
          @else
          <div id="family_icon" style="width:40px;height:40px;border-radius:50%;background:var(--tint);color:var(--red-700);display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
            <i class="fa-solid fa-users-viewfinder"></i>
          </div>
          @endif
          <p style="font-size:13px;font-weight:600;color:var(--ink)" data-en="Family Picture">পারিবারিক ছবি
            <span style="color:var(--muted);font-size:11px;font-weight:400" data-en="(Optional)">(ঐচ্ছিক)</span>
          </p>
          <p style="font-size:11px;color:var(--muted);margin-top:4px" data-en="For the Reunion Souvenir Album">রিইউনিয়ন স্মরণিকার জন্য</p>
          <input type="file" name="family_photo" id="family_photo" accept="image/*" style="display:none"
            onchange="previewUpload(this,'family_preview','family_icon')">
          <label for="family_photo" style="display:inline-block;margin-top:10px;cursor:pointer;padding:6px 12px;border-radius:8px;background:var(--tint);border:1px solid var(--line);font-size:12px;font-weight:600;color:var(--ink)" data-en="Browse Image">ছবি বেছে নিন</label>
        </div>
      </div>
    </div>

    {{-- SECTION 6: Fee Summary --}}
    <div style="background:var(--red-950,#3b0a12);color:#fff;border-radius:12px;padding:20px">
      <p style="font-size:11px;font-weight:600;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;display:flex;align-items:center;gap:6px" data-en="Registration Fee">
        <i class="fa-solid fa-calculator"></i> রেজিস্ট্রেশন ফি
      </p>
      <div style="display:grid;gap:8px;font-size:13px">
        <div style="display:flex;justify-content:space-between"><span data-en="Member (spouse & children included)">ব্যাচের সদস্য (স্ত্রী ও সন্তান সহ)</span><span style="font-weight:700">৳ 2,002</span></div>
        <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.5)"><span data-en="+ Guest (each)">+ গেস্ট (জনপ্রতি)</span><span>৳ 1,000</span></div>
        <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.5)"><span data-en="+ Driver">+ ড্রাইভার</span><span>৳ 500</span></div>
        <div style="display:flex;justify-content:space-between;color:rgba(255,255,255,.5)"><span data-en="+ Donation">+ ডোনেশন</span><span id="donationDisplay">৳ 0</span></div>
      </div>
      <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,.1);display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px">
        <div style="display:flex;justify-content:space-between">
          <span style="color:rgba(255,255,255,.5)" data-en="Children:">সন্তান: <span id="childCountDisplay" style="color:#fff;font-weight:700">{{ count($childrenDetails) }}</span></span>
          <span style="color:rgba(255,255,255,.7)" id="childSubtotal">{{ count($childrenDetails) > 0 ? 'Included' : '+ ৳ 0' }}</span>
        </div>
        <div style="display:flex;justify-content:space-between">
          <span style="color:rgba(255,255,255,.5)" data-en="Guests:">গেস্ট: <span id="guestCountDisplay" style="color:#fff;font-weight:700">{{ count($guestsDetails) }}</span></span>
          <span style="color:rgba(255,255,255,.7)" id="guestSubtotal">+ ৳ {{ count($guestsDetails) * 1000 }}</span>
        </div>
      </div>
      <div style="margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,.2);display:flex;justify-content:space-between;align-items:flex-end">
        <div>
          <span style="font-size:11px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.06em;font-weight:600" data-en="Total Payable">মোট পরিশোধযোগ্য</span>
          <p style="font-size:11px;color:rgba(255,255,255,.4);margin-top:2px" id="feeBreakdown">ব্যাচের সদস্য (৳2,002)</p>
        </div>
        <span style="font-size:26px;font-weight:700;color:#4ade80" id="grandTotal">৳ {{ number_format($reg ? $reg->total_amount : 2002, 0) }}</span>
      </div>
    </div>

    {{-- Submit --}}
    <div class="panel">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:15px;padding:13px">
        <i class="fa-solid fa-floppy-disk"></i>
        <span data-en="{{ $reg ? 'Update Registration' : 'Save Registration' }} & Proceed to Payment →">
          {{ $reg ? 'রেজিস্ট্রেশন আপডেট' : 'রেজিস্ট্রেশন সংরক্ষণ' }} → পেমেন্টে যান
        </span>
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
  <div style="margin-top:16px">
    @if($fullyPaid)
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:20px;display:flex;align-items:center;gap:12px">
      <span style="font-size:28px">✅</span>
      <div>
        <p style="font-weight:600;color:#166534;font-size:14px" data-en="Payment Confirmed">পেমেন্ট নিশ্চিত — ৳{{ number_format($reg->total_amount, 0) }}</p>
        <p style="color:#15803d;font-size:12px" data-en="Your spot at the reunion is secured.">রিইউনিয়নে আপনার স্থান নিশ্চিত। পদ্ধতি: {{ strtoupper($reg->payment_method ?? 'SSLCommerz') }}</p>
      </div>
    </div>
    @elseif($hasPending)
    <div style="background:#eff6ff;border:1px solid #93c5fd;border-radius:10px;padding:20px">
      <p style="font-weight:600;color:#1e40af;font-size:14px;margin-bottom:4px" data-en="⏳ Payment Pending Verification">⏳ পেমেন্ট যাচাই চলছে</p>
      <p style="color:#1d4ed8;font-size:12px">রেফারেন্স: <strong>{{ $reg->payment_reference }}</strong> — {{ strtoupper($reg->payment_method ?? '—') }}। অ্যাডমিন শীঘ্রই নিশ্চিত করবেন।</p>
      @if($paidAmount > 0)
      <p style="color:#1d4ed8;font-size:12px;margin-top:6px">পূর্বে নিশ্চিত: <strong>৳{{ number_format($paidAmount) }}</strong> · বাকি যাচাই: <strong>৳{{ number_format($balanceDue) }}</strong></p>
      @endif
    </div>
    @elseif($needsMore)
    @if($paidAmount > 0)
    <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:14px;margin-bottom:14px;display:flex;align-items:flex-start;gap:10px">
      <span style="font-size:18px;margin-top:2px">⚠️</span>
      <div>
        <p style="font-weight:600;color:#92400e;font-size:13px" data-en="Additional Payment Required">অতিরিক্ত পেমেন্ট প্রয়োজন</p>
        <p style="color:#b45309;font-size:12px;margin-top:3px">
          পরিশোধিত: <strong>৳{{ number_format($paidAmount) }}</strong> ·
          নতুন মোট: <strong>৳{{ number_format($reg->total_amount) }}</strong> ·
          বাকি: <strong>৳{{ number_format($balanceDue) }}</strong>
        </p>
      </div>
    </div>
    @endif
    <div class="panel" style="display:grid;gap:16px">
      <div>
        <p style="font-weight:600;color:var(--ink);font-size:14px;margin-bottom:4px">
          💳 {{ $paidAmount > 0 ? 'অতিরিক্ত পেমেন্ট' : 'পেমেন্ট প্রয়োজন' }} — ৳{{ number_format($balanceDue, 0) }}
        </p>
        <p style="color:var(--muted);font-size:12px" data-en="Choose your preferred payment method below.">নিচে আপনার পছন্দের পেমেন্ট পদ্ধতি বেছে নিন।</p>
      </div>

      {{-- Method Tabs --}}
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:8px">
        <button type="button" class="pay-tab" data-tab="bkash"
          style="padding:10px 4px;border-radius:10px;border:2px solid var(--red-700);background:var(--tint);text-align:center;cursor:pointer">
          <span style="font-size:13px;font-weight:800;display:block;color:#E2136E">bKash</span>
          <span style="font-size:10px;color:var(--muted)">Send Money</span>
        </button>
        <button type="button" class="pay-tab" data-tab="rocket"
          style="padding:10px 4px;border-radius:10px;border:2px solid var(--line);background:transparent;text-align:center;cursor:pointer">
          <span style="font-size:13px;font-weight:800;display:block;color:#8B1A8B">Rocket</span>
          <span style="font-size:10px;color:var(--muted)">Send Money</span>
        </button>
      </div>

      {{-- bKash --}}
      <div id="tab-bkash" class="pay-panel" style="background:var(--tint);border-radius:10px;padding:16px;border:1px solid var(--line);display:grid;gap:12px">
        <div style="font-size:12px;color:var(--muted);background:var(--surface);border-radius:8px;padding:12px;border:1px solid var(--line);display:grid;gap:6px">
          <p>1. <strong>bKash App</strong> খুলুন বা <strong>*247#</strong> ডায়াল করুন</p>
          <p>2. <strong>"Send Money"</strong> → <strong style="color:#E2136E;font-family:monospace">+880 1711-740273</strong></p>
          <p>3. পরিমাণ: <strong style="color:var(--red-700)">৳ {{ number_format($balanceDue, 0) }}</strong></p>
          <p>4. রেফারেন্স: আপনার <strong>নাম বা ফোন নম্বর</strong></p>
        </div>
        <form method="POST" action="{{ route('payment.manual') }}" style="display:grid;gap:10px">
          @csrf
          <input type="hidden" name="payment_method" value="bkash">
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:4px" data-en="Sender bKash Mobile *">প্রেরকের bKash নম্বর <span style="color:var(--red-700)">*</span></label>
            <input type="tel" name="sender_number" placeholder="01XXXXXXXXX">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:4px" data-en="bKash Transaction ID *">bKash ট্রানজেকশন ID <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="payment_reference" required placeholder="e.g. BL78X90A1" style="font-family:monospace;text-transform:uppercase">
          </div>
          <button type="submit" style="width:100%;padding:10px;border-radius:10px;color:#fff;font-weight:700;font-size:13px;border:none;cursor:pointer;background:#E2136E" data-en="Verify & Confirm bKash Payment">bKash পেমেন্ট নিশ্চিত করুন</button>
        </form>
      </div>

      {{-- Rocket --}}
      <div id="tab-rocket" class="pay-panel" style="background:var(--tint);border-radius:10px;padding:16px;border:1px solid var(--line);display:none;gap:12px">
        <div style="font-size:12px;color:var(--muted);background:var(--surface);border-radius:8px;padding:12px;border:1px solid var(--line);display:grid;gap:6px">
          <p>1. <strong>Rocket App</strong> খুলুন বা <strong>*322#</strong> ডায়াল করুন</p>
          <p>2. <strong>"Send Money"</strong> → <strong style="color:#8B1A8B;font-family:monospace">+880 1711-740273</strong></p>
          <p>3. পরিমাণ: <strong style="color:var(--red-700)">৳ {{ number_format($balanceDue, 0) }}</strong></p>
          <p>4. রেফারেন্স: আপনার <strong>নাম বা ফোন নম্বর</strong></p>
        </div>
        <form method="POST" action="{{ route('payment.manual') }}" style="display:grid;gap:10px">
          @csrf
          <input type="hidden" name="payment_method" value="rocket">
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:4px">প্রেরকের Rocket নম্বর <span style="color:var(--red-700)">*</span></label>
            <input type="tel" name="sender_number" placeholder="01XXXXXXXXX">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:4px">Rocket ট্রানজেকশন ID <span style="color:var(--red-700)">*</span></label>
            <input type="text" name="payment_reference" required placeholder="e.g. TXN1234567890" style="font-family:monospace;text-transform:uppercase">
          </div>
          <button type="submit" style="width:100%;padding:10px;border-radius:10px;color:#fff;font-weight:700;font-size:13px;border:none;cursor:pointer;background:#8B1A8B">Rocket পেমেন্ট নিশ্চিত করুন</button>
        </form>
      </div>
    </div>
    @endif
  </div>
  @endif

</div>
</div>

<script>
// ── Photo preview ─────────────────────────────────────────────────
function previewUpload(input, previewId, iconId) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    let img = document.getElementById(previewId);
    if (!img) {
      img = document.createElement('img');
      img.id = previewId;
      img.style.cssText = 'width:80px;height:96px;margin:0 auto 12px;object-fit:cover;border-radius:8px;border:2px solid var(--line);display:block';
      const icon = document.getElementById(iconId);
      if (icon) icon.replaceWith(img);
      else input.parentElement.prepend(img);
    }
    img.src = e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
}

// ── Shift selector ────────────────────────────────────────────────
function selectShift(label) {
  document.querySelectorAll('.shift-opt .shift-face').forEach(f => {
    f.style.borderColor = 'var(--line)';
    f.style.background = '';
    f.style.color = 'var(--muted)';
  });
  const face = label.querySelector('.shift-face');
  face.style.borderColor = 'var(--red-700)';
  face.style.background = 'var(--tint)';
  face.style.color = 'var(--red-700)';
  label.querySelector('input').checked = true;
}
document.querySelectorAll('.shift-opt').forEach(label => {
  label.addEventListener('click', () => selectShift(label));
  if (label.querySelector('input').checked) selectShift(label);
});

// ── T-Shirt size selector ─────────────────────────────────────────
function selectSize(label, size) {
  document.querySelectorAll('.size-opt .size-face').forEach(f => {
    f.style.borderColor = 'var(--line)';
    f.style.background = '';
  });
  const face = label.querySelector('.size-face');
  face.style.borderColor = 'var(--red-700)';
  face.style.background = 'var(--tint)';
  label.querySelector('input').checked = true;
}
document.querySelectorAll('.size-opt').forEach(label => {
  if (label.querySelector('input').checked) selectSize(label, label.querySelector('input').value);
});

// ── Dynamic children ──────────────────────────────────────────────
let childIdx = {{ count($childrenDetails) }};
document.getElementById('btnAddChild').addEventListener('click', () => {
  const i = childIdx++;
  const card = document.createElement('div');
  card.className = 'child-card';
  card.style.cssText = 'border:1px solid var(--line);border-radius:8px;padding:12px';
  card.innerHTML = `
    <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line);padding-bottom:8px;margin-bottom:10px">
      <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em"><i class="fa-solid fa-child" style="color:var(--red-700)"></i> সন্তানের বিবরণ</span>
      <button type="button" class="remove-child" style="font-size:12px;color:var(--red-700);background:none;border:none;cursor:pointer;font-weight:600"><i class="fa-solid fa-trash-can"></i> সরান</button>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px">
      <div>
        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px">নাম (ইংরেজি) <span style="color:var(--red-700)">*</span></label>
        <input type="text" name="children_details[${i}][name]" required placeholder="e.g. Abrar Hasan">
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px">নাম (বাংলায়)</label>
        <input type="text" name="children_details[${i}][name_bn]" style="font-family:var(--f-body)" placeholder="যেমন: আবরার হাসান">
      </div>
    </div>`;
  card.querySelector('.remove-child').addEventListener('click', () => { card.remove(); calcFees(); });
  document.getElementById('childrenContainer').appendChild(card);
  document.getElementById('emptyChildrenNotice').style.display = 'none';
  calcFees();
});

document.querySelectorAll('.remove-child').forEach(btn => {
  btn.addEventListener('click', () => { btn.closest('.child-card').remove(); calcFees(); });
});

// ── Dynamic guests ────────────────────────────────────────────────
let guestIdx = {{ count($guestsDetails) }};
document.getElementById('btnAddGuest').addEventListener('click', () => {
  const i = guestIdx++;
  const card = document.createElement('div');
  card.className = 'guest-card';
  card.style.cssText = 'border:1px solid var(--line);border-radius:8px;padding:12px';
  card.innerHTML = `
    <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line);padding-bottom:8px;margin-bottom:10px">
      <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em"><i class="fa-solid fa-user-plus" style="color:var(--red-700)"></i> গেস্টের বিবরণ</span>
      <button type="button" class="remove-guest" style="font-size:12px;color:var(--red-700);background:none;border:none;cursor:pointer;font-weight:600"><i class="fa-solid fa-trash-can"></i> সরান</button>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px">
      <div>
        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px">পুরো নাম <span style="color:var(--red-700)">*</span></label>
        <input type="text" name="guests_details[${i}][name]" required placeholder="Full name">
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px">সম্পর্ক</label>
        <input type="text" name="guests_details[${i}][relationship]" placeholder="e.g. Brother">
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:3px">যোগাযোগ</label>
        <input type="tel" name="guests_details[${i}][contact]" placeholder="+880 1XXXXXXXXX">
      </div>
    </div>`;
  card.querySelector('.remove-guest').addEventListener('click', () => { card.remove(); calcFees(); });
  document.getElementById('guestsContainer').appendChild(card);
  document.getElementById('emptyGuestsNotice').style.display = 'none';
  calcFees();
});

document.querySelectorAll('.remove-guest').forEach(btn => {
  btn.addEventListener('click', () => { btn.closest('.guest-card').remove(); calcFees(); });
});

// ── Same-address toggle ───────────────────────────────────────────
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

// ── Live fee calculator ───────────────────────────────────────────
function calcFees() {
  const children = document.querySelectorAll('.child-card').length;
  const guests   = document.querySelectorAll('.guest-card').length;
  const driver   = document.getElementById('driver_included').checked;
  const donation = parseInt(document.getElementById('donation_amount').value) || 0;
  const total    = 2002 + (guests * 1000) + (driver ? 500 : 0) + donation;

  document.getElementById('childCountDisplay').textContent = children;
  document.getElementById('childSubtotal').textContent     = children > 0 ? 'Included' : '+ ৳ 0';
  document.getElementById('guestCountDisplay').textContent = guests;
  document.getElementById('guestSubtotal').textContent     = '+ ৳ ' + (guests * 1000).toLocaleString();
  document.getElementById('donationDisplay').textContent   = '৳ ' + donation.toLocaleString();
  document.getElementById('grandTotal').textContent        = '৳ ' + total.toLocaleString();

  const parts = ['ব্যাচের সদস্য (৳2,002)'];
  if (guests)   parts.push(`${guests} গেস্ট (৳${(guests * 1000).toLocaleString()})`);
  if (driver)   parts.push('ড্রাইভার (৳500)');
  if (donation) parts.push(`ডোনেশন (৳${donation.toLocaleString()})`);
  document.getElementById('feeBreakdown').textContent = parts.join(' + ');

  const emptyChildren = document.getElementById('emptyChildrenNotice');
  const emptyGuests   = document.getElementById('emptyGuestsNotice');
  if (emptyChildren) emptyChildren.style.display = children > 0 ? 'none' : '';
  if (emptyGuests)   emptyGuests.style.display   = guests > 0 ? 'none' : '';
}

calcFees();

// ── Payment method tabs ───────────────────────────────────────────
document.querySelectorAll('.pay-tab').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.pay-tab').forEach(b => {
      b.style.borderColor = 'var(--line)';
      b.style.background = 'transparent';
    });
    this.style.borderColor = 'var(--red-700)';
    this.style.background = 'var(--tint)';
    document.querySelectorAll('.pay-panel').forEach(p => p.style.display = 'none');
    document.getElementById('tab-' + this.dataset.tab).style.display = 'grid';
  });
});
</script>
@endsection
