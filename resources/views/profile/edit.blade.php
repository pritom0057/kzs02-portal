@extends('layouts.app')
@section('title', 'প্রোফাইল সম্পাদনা — KZS 2002')

@section('content')
<div class="m-main">
<div class="wrap">
<div class="col" style="margin-inline:auto">

  {{-- Cover --}}
  <div style="border-radius:12px;overflow:hidden;margin-bottom:24px;position:relative;height:96px;background:linear-gradient(135deg,var(--red-700),var(--red-900))">
    <div style="position:absolute;inset:0;opacity:.08;background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:14px 14px"></div>
    <div style="position:absolute;top:12px;right:16px;color:rgba(255,255,255,.25);font-size:40px;font-weight:900;font-family:var(--f-display);user-select:none">KZS</div>
    <div style="position:absolute;bottom:16px;left:20px;right:20px;display:flex;align-items:flex-end;justify-content:space-between">
      <div>
        <h1 style="font-size:18px;font-weight:700;color:#fff;line-height:1.2" data-en="Edit Profile">প্রোফাইল সম্পাদনা</h1>
        <p style="color:rgba(255,255,255,.6);font-size:12px;margin-top:2px">KZS · SSC Batch 2002</p>
      </div>
      <a href="{{ route('dashboard') }}" style="color:rgba(255,255,255,.7);font-size:13px;text-decoration:none" data-en="← Dashboard">← ড্যাশবোর্ড</a>
    </div>
  </div>

  <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="display:grid;gap:16px">
    @csrf

    {{-- Photo --}}
    <div class="panel">
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px" data-en="Profile Photo">প্রোফাইল ছবি</p>
      <div style="display:flex;align-items:center;gap:16px">
        <div style="width:64px;height:64px;border-radius:50%;overflow:hidden;background:var(--tint);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:var(--red-700);font-family:var(--f-display)">
          @if($alumni->photo_url)
            <img src="{{ Storage::url($alumni->photo_url) }}" style="width:100%;height:100%;object-fit:cover" id="photo-preview">
          @else
            <span id="photo-placeholder">{{ strtoupper(substr($alumni->name, 0, 1)) }}</span>
          @endif
        </div>
        <div>
          <input type="file" name="photo" id="photo" accept="image/*" style="display:none" onchange="previewPhoto(this)">
          <label for="photo" style="cursor:pointer;display:inline-block;background:var(--tint);color:var(--ink);font-size:13px;font-weight:500;padding:6px 14px;border-radius:8px;border:1px solid var(--line)" data-en="Choose Photo">ছবি বেছে নিন</label>
          <p style="color:var(--muted);font-size:12px;margin-top:4px" data-en="JPG, PNG or WebP — max 2MB">JPG, PNG বা WebP — সর্বোচ্চ ২ MB</p>
        </div>
      </div>
    </div>

    {{-- Basic Info --}}
    <div class="panel">
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px" data-en="Basic Information">মৌলিক তথ্য</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div style="grid-column:1/-1;background:var(--tint);border-radius:8px;padding:10px 12px;font-size:13px">
          <span style="color:var(--muted)" data-en="Email:">ইমেইল:</span>
          <span style="font-weight:500;color:var(--ink);margin-left:4px">{{ $alumni->email }}</span>
        </div>
        <div style="grid-column:1/-1">
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Full Name (English)">পুরো নাম (ইংরেজিতে)</label>
          <input type="text" name="name" value="{{ old('name', $alumni->name) }}" required placeholder="Your full name"
            style="@error('name') border-color:var(--red-700); @enderror">
          @error('name') <p style="color:var(--red-700);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
        </div>
        @foreach([
          ['roll_number','SSC রোল নম্বর','SSC Roll Number','text','','Leave blank if not known'],
          ['mobile','মোবাইল নম্বর','Mobile Number','text','01XXXXXXXXX','01XXXXXXXXX'],
          ['whatsapp','WhatsApp নম্বর','WhatsApp Number','text','01XXXXXXXXX','01XXXXXXXXX'],
          ['emergency_contact','জরুরি যোগাযোগ','Emergency Contact','text','01XXXXXXXXX','01XXXXXXXXX'],
          ['father_name','বাবার নাম',"Father's Name",'text','',''],
          ['mother_name','মায়ের নাম',"Mother's Name",'text','',''],
        ] as [$name, $label, $en, $type, $placeholder, $placeholder_en])
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="{{ $en }}">{{ $label }}</label>
          <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $alumni->$name) }}"
            placeholder="{{ $placeholder }}"
            style="@error($name) border-color:var(--red-700); @enderror">
          @error($name) <p style="color:var(--red-700);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
        </div>
        @endforeach
      </div>
    </div>

    {{-- Address --}}
    @php
      function parseAddrProfile(string $addr): array {
        $p = ['road'=>'','post'=>'','thana'=>'','postcode'=>'','district'=>''];
        if (preg_match('/^(.*?),\s*PO:\s*(.*?),\s*(.*?),\s*(?:PC:|Upazilla?:)\s*(.*?),\s*(.*)$/', $addr, $m)) {
          $p = ['road'=>trim($m[1]),'post'=>trim($m[2]),'thana'=>trim($m[3]),'postcode'=>trim($m[4]),'district'=>trim($m[5])];
        }
        return $p;
      }
      $pres = parseAddrProfile($alumni->present_address ?? '');
      $perm = parseAddrProfile($alumni->permanent_address ?? '');
      if (old('pres_road')) $pres = ['road'=>old('pres_road'),'post'=>old('pres_post'),'thana'=>old('pres_thana'),'postcode'=>old('pres_postcode'),'district'=>old('pres_district')];
      if (old('perm_road')) $perm = ['road'=>old('perm_road'),'post'=>old('perm_post'),'thana'=>old('perm_thana'),'postcode'=>old('perm_postcode'),'district'=>old('perm_district')];
    @endphp
    <div class="panel">
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px" data-en="Address">ঠিকানা</p>

      {{-- Present --}}
      <p style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px" data-en="Present Address">বর্তমান ঠিকানা</p>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px">
        <div style="grid-column:1/-1">
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Village / Road / House">গ্রাম / রাস্তা / বাড়ি</label>
          <input type="text" name="pres_road" id="prof_pres_road" value="{{ $pres['road'] }}" placeholder="House, Road, Area">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Post Office">পোস্ট অফিস</label>
          <input type="text" name="pres_post" id="prof_pres_post" value="{{ $pres['post'] }}" placeholder="Post Office">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Thana">থানা</label>
          <input type="text" name="pres_thana" id="prof_pres_thana" value="{{ $pres['thana'] }}" placeholder="Thana">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Upazilla">উপজেলা</label>
          <input type="text" name="pres_postcode" id="prof_pres_postcode" value="{{ $pres['postcode'] }}" placeholder="e.g. Kushtia Sadar">
        </div>
        <div style="grid-column:1/-1">
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="District">জেলা</label>
          <input type="text" name="pres_district" id="prof_pres_district" value="{{ $pres['district'] }}" placeholder="District">
        </div>
      </div>

      {{-- Permanent --}}
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <p style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em" data-en="Permanent Address">স্থায়ী ঠিকানা</p>
        <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:var(--ink);cursor:pointer;background:var(--tint);padding:5px 10px;border-radius:8px;border:1px solid var(--line)">
          <input type="checkbox" id="prof_sameAddr" style="width:14px;height:14px;accent-color:var(--red-700)">
          <span data-en="Same as Present">বর্তমানের মতো</span>
        </label>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
        <div style="grid-column:1/-1">
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Village / Road / House">গ্রাম / রাস্তা / বাড়ি</label>
          <input type="text" name="perm_road" id="prof_perm_road" value="{{ $perm['road'] }}" placeholder="House, Road, Area">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Post Office">পোস্ট অফিস</label>
          <input type="text" name="perm_post" id="prof_perm_post" value="{{ $perm['post'] }}" placeholder="Post Office">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Thana">থানা</label>
          <input type="text" name="perm_thana" id="prof_perm_thana" value="{{ $perm['thana'] }}" placeholder="Thana">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Upazilla">উপজেলা</label>
          <input type="text" name="perm_postcode" id="prof_perm_postcode" value="{{ $perm['postcode'] }}" placeholder="e.g. Kushtia Sadar">
        </div>
        <div style="grid-column:1/-1">
          <label style="display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="District">জেলা</label>
          <input type="text" name="perm_district" id="prof_perm_district" value="{{ $perm['district'] }}" placeholder="District">
        </div>
      </div>
    </div>

    {{-- School Info --}}
    <div class="panel">
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px" data-en="School Information">স্কুলের তথ্য</p>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Studied Up To">কত ক্লাস পর্যন্ত</label>
          <select name="school_class">
            <option value="" data-en="Select">বেছে নিন</option>
            @foreach(['Class 10 (Completed SSC 2002)','Class 9','Class 8','Class 7','Class 6','Class 5','Class 4','Class 3'] as $cls)
            <option value="{{ $cls }}" {{ old('school_class', $alumni->school_class) === $cls ? 'selected' : '' }}>{{ $cls }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Shift">শিফট</label>
          <select name="school_shift">
            <option value="" data-en="Select">বেছে নিন</option>
            @foreach(['Morning','Day'] as $s)
            <option value="{{ $s }}" {{ old('school_shift', $alumni->school_shift) === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Section">সেকশন</label>
          <select name="section">
            <option value="" data-en="Select">বেছে নিন</option>
            @foreach(['A','B','C','D'] as $s)
            <option value="{{ $s }}" {{ old('section', $alumni->section) === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div style="grid-column:1/-1">
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Higher Education">উচ্চশিক্ষা</label>
          <input type="text" name="higher_education" value="{{ old('higher_education', $alumni->higher_education) }}"
            placeholder="e.g. BSc Engineering">
        </div>
      </div>
    </div>

    {{-- Professional --}}
    <div class="panel">
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px" data-en="Professional Information">পেশাগত তথ্য</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        @foreach([
          ['current_profession','পেশা','Occupation','e.g. Software Engineer'],
          ['organization','প্রতিষ্ঠান','Organization','Company / Institution'],
          ['designation','পদবি','Designation','e.g. Senior Manager'],
          ['current_location','বর্তমান অবস্থান','Current Location','e.g. Dhaka, Bangladesh'],
        ] as [$name, $label, $en, $placeholder])
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="{{ $en }}">{{ $label }}</label>
          <input type="text" name="{{ $name }}" value="{{ old($name, $alumni->$name) }}" placeholder="{{ $placeholder }}">
        </div>
        @endforeach
      </div>
    </div>

    {{-- Family --}}
    <div class="panel">
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px" data-en="Family">পরিবার</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Wife's Name">স্ত্রীর নাম</label>
          <input type="text" name="spouse_name" value="{{ old('spouse_name', $alumni->spouse_name) }}">
        </div>
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Wife's Contact">স্ত্রীর যোগাযোগ</label>
          <input type="text" name="spouse_contact" value="{{ old('spouse_contact', $alumni->spouse_contact) }}">
        </div>
      </div>
    </div>

    {{-- Social --}}
    <div class="panel">
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px" data-en="Social Links">সোশ্যাল লিংক</p>
      <div style="display:grid;gap:12px">
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="Facebook URL">Facebook URL</label>
          <input type="url" name="facebook_url" value="{{ old('facebook_url', $alumni->facebook_url) }}"
            placeholder="https://facebook.com/yourname">
        </div>
        <div>
          <label style="display:block;font-size:13px;font-weight:500;color:var(--muted);margin-bottom:4px" data-en="LinkedIn URL">LinkedIn URL</label>
          <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $alumni->linkedin_url) }}"
            placeholder="https://linkedin.com/in/yourname">
        </div>
        <div style="display:flex;align-items:center;gap:8px;padding-top:4px">
          <input type="checkbox" name="show_in_directory" id="show_in_directory" value="1"
            style="width:16px;height:16px;accent-color:var(--red-700)"
            {{ old('show_in_directory', $alumni->show_in_directory ?? true) ? 'checked' : '' }}>
          <label for="show_in_directory" style="font-size:13px;color:var(--ink);cursor:pointer" data-en="Show my profile in the Alumni Directory">অ্যালামনাই ডিরেক্টরিতে আমার প্রোফাইল দেখান</label>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:15px;padding:12px" data-en="Save Changes">
      পরিবর্তন সংরক্ষণ করুন
    </button>
  </form>

</div>
</div>
</div>

<script>
(function() {
  const presIds = ['prof_pres_road','prof_pres_post','prof_pres_thana','prof_pres_postcode','prof_pres_district'];
  const permIds = ['prof_perm_road','prof_perm_post','prof_perm_thana','prof_perm_postcode','prof_perm_district'];
  const cb = document.getElementById('prof_sameAddr');
  cb.addEventListener('change', function() {
    permIds.forEach((id, i) => {
      const el = document.getElementById(id);
      el.readOnly = this.checked;
      if (this.checked) el.value = document.getElementById(presIds[i]).value;
    });
  });
  presIds.forEach(id => document.getElementById(id).addEventListener('input', () => {
    if (cb.checked) {
      permIds.forEach((pid, i) => document.getElementById(pid).value = document.getElementById(presIds[i]).value);
    }
  }));
})();

function previewPhoto(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      let preview = document.getElementById('photo-preview');
      const placeholder = document.getElementById('photo-placeholder');
      if (!preview) {
        preview = document.createElement('img');
        preview.id = 'photo-preview';
        preview.style.cssText = 'width:100%;height:100%;object-fit:cover';
        if (placeholder) placeholder.replaceWith(preview);
      }
      preview.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
@endsection
