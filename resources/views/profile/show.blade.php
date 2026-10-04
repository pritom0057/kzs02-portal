@extends('layouts.app')
@section('title', 'আমার প্রোফাইল — KZS 2002')

@section('content')
@php
  $photoSrc = $alumni->photo_url
    ? (str_starts_with($alumni->photo_url, 'uploads/') ? asset($alumni->photo_url) : asset('storage/' . $alumni->photo_url))
    : null;
@endphp

<div class="m-main">
<div class="wrap">
<div class="col" style="margin-inline:auto">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
    <h1 class="m-title" style="font-size:22px" data-en="My Profile">আমার প্রোফাইল</h1>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
      <i class="fa fa-pen" style="font-size:12px"></i> <span data-en="Edit Profile">প্রোফাইল সম্পাদনা</span>
    </a>
  </div>

  <div class="panel" style="margin-bottom:16px">
    {{-- Photo & name --}}
    <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--line);flex-wrap:wrap">
      <div style="width:80px;height:80px;border-radius:50%;overflow:hidden;background:var(--tint);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:var(--red-700);font-family:var(--f-display)">
        @if($photoSrc)
          <img src="{{ $photoSrc }}" alt="{{ $alumni->name }}" style="width:100%;height:100%;object-fit:cover">
        @else
          {{ strtoupper(substr($alumni->name, 0, 1)) }}
        @endif
      </div>
      <div>
        <h2 style="font-size:20px;font-weight:700;color:var(--red-900)">{{ $alumni->name }}</h2>
        <p style="color:var(--muted);font-size:14px" data-en="SSC Roll:">SSC রোল: {{ $alumni->roll_number }}</p>
        <span class="badge badge-green" style="margin-top:6px;display:inline-block" data-en="Verified">যাচাইকৃত</span>
      </div>
    </div>

    {{-- Info rows --}}
    <dl style="display:grid;gap:14px;font-size:14px">
      @php
        $fields = [
          ['label' => 'ইমেইল', 'en' => 'Email', 'val' => $alumni->email],
          ['label' => 'মোবাইল', 'en' => 'Mobile', 'val' => $alumni->mobile ?: ($alumni->phone ?: '—')],
          ['label' => 'জরুরি যোগাযোগ', 'en' => 'Emergency Contact', 'val' => $alumni->emergency_contact ?? null],
          ['label' => 'WhatsApp', 'en' => 'WhatsApp', 'val' => $alumni->whatsapp ?? null],
          ['label' => 'স্ত্রী/স্বামী', 'en' => 'Spouse', 'val' => $alumni->spouse_name ?: '—'],
          ['label' => 'বর্তমান ঠিকানা', 'en' => 'Present Address', 'val' => $alumni->present_address ?: '—'],
          ['label' => 'পেশা', 'en' => 'Profession', 'val' => $alumni->current_profession ?: '—'],
          ['label' => 'অবস্থান', 'en' => 'Location', 'val' => $alumni->current_location ?: '—'],
        ];
      @endphp
      @foreach($fields as $f)
        @if($f['val'] !== null)
        <div style="display:flex;gap:16px">
          <dt style="width:160px;color:var(--muted);font-weight:500;flex-shrink:0" data-en="{{ $f['en'] }}">{{ $f['label'] }}</dt>
          <dd style="color:var(--ink);word-break:break-word">{{ $f['val'] }}</dd>
        </div>
        @endif
      @endforeach
    </dl>
  </div>

  {{-- Event registration summary --}}
  @if($alumni->eventRegistration)
  <div class="panel">
    <h3 style="font-weight:600;color:var(--ink);margin-bottom:14px;font-size:15px" data-en="Event Registration">ইভেন্ট রেজিস্ট্রেশন</h3>
    <dl style="display:grid;gap:12px;font-size:14px">
      <div style="display:flex;gap:16px">
        <dt style="width:160px;color:var(--muted);font-weight:500;flex-shrink:0" data-en="Guests">গেস্ট সংখ্যা</dt>
        <dd style="color:var(--ink)">{{ $alumni->eventRegistration->guest_count }}</dd>
      </div>
      <div style="display:flex;gap:16px">
        <dt style="width:160px;color:var(--muted);font-weight:500;flex-shrink:0" data-en="T-Shirt Size">টি-শার্ট সাইজ</dt>
        <dd style="color:var(--ink)">{{ $alumni->eventRegistration->tshirt_size ?: '—' }}</dd>
      </div>
      <div style="display:flex;gap:16px">
        <dt style="width:160px;color:var(--muted);font-weight:500;flex-shrink:0" data-en="Dietary Notes">খাদ্য সংক্রান্ত নোট</dt>
        <dd style="color:var(--ink)">{{ $alumni->eventRegistration->dietary_notes ?: '—' }}</dd>
      </div>
      <div style="display:flex;gap:16px">
        <dt style="width:160px;color:var(--muted);font-weight:500;flex-shrink:0" data-en="Payment">পেমেন্ট</dt>
        <dd>
          @php $ps = $alumni->eventRegistration->payment_status; @endphp
          <span class="badge {{ $ps === 'paid' ? 'badge-green' : ($ps === 'pending' ? 'badge-blue' : 'badge-yellow') }}">
            {{ $ps === 'paid' ? 'পেমেন্ট সম্পন্ন' : ($ps === 'pending' ? 'যাচাইয়ে আছে' : 'পেমেন্ট বাকি') }}
          </span>
        </dd>
      </div>
    </dl>
  </div>
  @endif

</div>
</div>
</div>
@endsection
