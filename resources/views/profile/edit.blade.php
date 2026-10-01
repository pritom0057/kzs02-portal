@extends('layouts.app')
@section('title', 'Edit Profile — KZS 2002 Reunion')

@section('content')

{{-- Cover --}}
<div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
    <div class="h-24 sm:h-32 bg-gradient-to-br from-primary via-red-700 to-red-900 relative">
        <div class="absolute inset-0 opacity-10"
             style="background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:14px 14px"></div>
        <div class="absolute top-3 right-4 text-white/40 text-4xl sm:text-5xl font-black select-none">KZS</div>
        <div class="absolute bottom-4 left-5 flex items-end justify-between right-5">
            <div>
                <h1 class="text-xl font-bold text-white leading-tight">Edit Profile</h1>
                <p class="text-white/60 text-xs mt-0.5">KZS · SSC Batch 2002</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-white/70 hover:text-white text-sm font-medium transition">&larr; Dashboard</a>
        </div>
    </div>
</div>

<div class="max-w-2xl mx-auto">

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Photo --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-3">Profile Photo</p>
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0 flex items-center justify-center text-2xl font-bold text-gray-400 dark:text-gray-500">
                    @if($alumni->photo_url)
                        <img src="{{ Storage::url($alumni->photo_url) }}" class="w-full h-full object-cover" id="photo-preview">
                    @else
                        <span id="photo-placeholder">{{ strtoupper(substr($alumni->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                    <label for="photo" class="cursor-pointer bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-kgray dark:text-gray-300 text-sm font-medium px-4 py-2 rounded-lg transition">
                        Choose Photo
                    </label>
                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">JPG, PNG or WebP — max 2MB</p>
                </div>
            </div>
        </div>

        {{-- Basic Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-4">Basic Information</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-sm">
                    <span class="text-gray-400 dark:text-gray-500">Email:</span> <span class="font-medium dark:text-gray-200">{{ $alumni->email }}</span>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">Full Name (English)</label>
                    <input type="text" name="name" value="{{ old('name', $alumni->name) }}" required
                        placeholder="Your full name"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @foreach([
                    ['roll_number','SSC Roll Number','text','Leave blank if not known'],
                    ['mobile','Mobile Number','text','01XXXXXXXXX'],
                    ['whatsapp','WhatsApp Number','text','01XXXXXXXXX'],
                    ['emergency_contact','Emergency Contact','text','01XXXXXXXXX'],
                    ['father_name',"Father's Name",'text',''],
                    ['mother_name',"Mother's Name",'text',''],
                ] as [$name, $label, $type, $placeholder])
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">{{ $label }}</label>
                    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $alumni->$name) }}"
                        placeholder="{{ $placeholder }}"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen @error($name) border-red-400 @enderror">
                    @error($name) <p class="text-brand text-xs mt-1">{{ $message }}</p> @enderror
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
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-4">Address</p>

            {{-- Present Address --}}
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Present Address</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 mb-5">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">Village / Road / House</label>
                    <input type="text" name="pres_road" id="prof_pres_road" value="{{ $pres['road'] }}" placeholder="House, Road, Area"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div>
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">Post Office</label>
                    <input type="text" name="pres_post" id="prof_pres_post" value="{{ $pres['post'] }}" placeholder="Post Office"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div>
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">Thana</label>
                    <input type="text" name="pres_thana" id="prof_pres_thana" value="{{ $pres['thana'] }}" placeholder="Thana"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div>
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">Upazilla</label>
                    <input type="text" name="pres_postcode" id="prof_pres_postcode" value="{{ $pres['postcode'] }}" placeholder="e.g. Kushtia Sadar"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">District</label>
                    <input type="text" name="pres_district" id="prof_pres_district" value="{{ $pres['district'] }}" placeholder="District"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
            </div>

            {{-- Permanent Address --}}
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Permanent Address</p>
                <label class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300 cursor-pointer bg-gray-50 dark:bg-gray-700 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <input type="checkbox" id="prof_sameAddr" class="w-3.5 h-3.5 accent-kgreen"> Same as Present
                </label>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">Village / Road / House</label>
                    <input type="text" name="perm_road" id="prof_perm_road" value="{{ $perm['road'] }}" placeholder="House, Road, Area"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div>
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">Post Office</label>
                    <input type="text" name="perm_post" id="prof_perm_post" value="{{ $perm['post'] }}" placeholder="Post Office"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div>
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">Thana</label>
                    <input type="text" name="perm_thana" id="prof_perm_thana" value="{{ $perm['thana'] }}" placeholder="Thana"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div>
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">Upazilla</label>
                    <input type="text" name="perm_postcode" id="prof_perm_postcode" value="{{ $perm['postcode'] }}" placeholder="e.g. Kushtia Sadar"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-kgray dark:text-gray-400 mb-1">District</label>
                    <input type="text" name="perm_district" id="prof_perm_district" value="{{ $perm['district'] }}" placeholder="District"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
            </div>
        </div>

        {{-- School Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-4">School Information</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">Studied Up To</label>
                    <select name="school_class" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                        <option value="">Select</option>
                        @foreach(['Class 10 (Completed SSC 2002)','Class 9','Class 8','Class 7','Class 6','Class 5','Class 4','Class 3'] as $cls)
                        <option value="{{ $cls }}" {{ old('school_class', $alumni->school_class) === $cls ? 'selected' : '' }}>{{ $cls }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">Shift</label>
                    <select name="school_shift" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                        <option value="">Select</option>
                        @foreach(['Morning','Day'] as $s)
                        <option value="{{ $s }}" {{ old('school_shift', $alumni->school_shift) === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">Section</label>
                    <select name="section" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                        <option value="">Select</option>
                        @foreach(['A','B','C','D'] as $s)
                        <option value="{{ $s }}" {{ old('section', $alumni->section) === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">Higher Education</label>
                    <input type="text" name="higher_education" value="{{ old('higher_education', $alumni->higher_education) }}"
                        placeholder="e.g. BSc Engineering"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
            </div>
        </div>

        {{-- Professional --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-4">Professional Information</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['current_profession','Occupation','e.g. Software Engineer'],
                    ['organization','Organization','Company / Institution'],
                    ['designation','Designation','e.g. Senior Manager'],
                    ['current_location','Current Location','e.g. Dhaka, Bangladesh'],
                ] as [$name, $label, $placeholder])
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">{{ $label }}</label>
                    <input type="text" name="{{ $name }}" value="{{ old($name, $alumni->$name) }}"
                        placeholder="{{ $placeholder }}"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Family --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-4">Family</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">Spouse Name</label>
                    <input type="text" name="spouse_name" value="{{ old('spouse_name', $alumni->spouse_name) }}"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">Spouse Contact</label>
                    <input type="text" name="spouse_contact" value="{{ old('spouse_contact', $alumni->spouse_contact) }}"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
            </div>
        </div>

        {{-- Social --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-4">Social Links</p>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">Facebook URL</label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $alumni->facebook_url) }}"
                        placeholder="https://facebook.com/yourname"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div>
                    <label class="block text-sm font-medium text-kgray dark:text-gray-300 mb-1">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $alumni->linkedin_url) }}"
                        placeholder="https://linkedin.com/in/yourname"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-kgreen">
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="show_in_directory" id="show_in_directory" value="1"
                        class="w-4 h-4 accent-kgreen"
                        {{ old('show_in_directory', $alumni->show_in_directory ?? true) ? 'checked' : '' }}>
                    <label for="show_in_directory" class="text-sm text-kgray dark:text-gray-300">Show my profile in the Alumni Directory</label>
                </div>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-kgreen text-white font-semibold py-2.5 rounded-lg hover:opacity-90 transition">
            Save Changes
        </button>
    </form>
</div>

<script>
// Same-address toggle for profile edit
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
                preview.className = 'w-full h-full object-cover';
                if (placeholder) placeholder.replaceWith(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
