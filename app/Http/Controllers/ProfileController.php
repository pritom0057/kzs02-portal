<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['alumni' => auth()->user()]);
    }

    public function edit()
    {
        return view('profile.edit', ['alumni' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $alumni = auth()->user();

        $request->validate([
            'name'               => 'required|string|max:100',
            'phone'              => 'nullable|string|max:20',
            'mobile'             => 'nullable|string|max:20',
            'whatsapp'           => 'nullable|string|max:20',
            'emergency_contact'  => 'nullable|string|max:20',
            'roll_number'        => 'nullable|string|max:20|unique:alumni,roll_number,' . $alumni->id,
            'father_name'        => 'nullable|string|max:100',
            'mother_name'        => 'nullable|string|max:100',
            'pres_road'          => 'nullable|string|max:200',
            'pres_post'          => 'nullable|string|max:100',
            'pres_thana'         => 'nullable|string|max:100',
            'pres_postcode'      => 'nullable|string|max:20',
            'pres_district'      => 'nullable|string|max:100',
            'perm_road'          => 'nullable|string|max:200',
            'perm_post'          => 'nullable|string|max:100',
            'perm_thana'         => 'nullable|string|max:100',
            'perm_postcode'      => 'nullable|string|max:20',
            'perm_district'      => 'nullable|string|max:100',
            'current_profession' => 'nullable|string|max:100',
            'current_location'   => 'nullable|string|max:100',
            'school_class'       => 'nullable|string|max:60',
            'school_shift'       => 'nullable|in:Morning,Day',
            'section'            => 'nullable|in:A,B,C,D',
            'higher_education'   => 'nullable|string|max:150',
            'organization'       => 'nullable|string|max:150',
            'designation'        => 'nullable|string|max:150',
            'spouse_name'        => 'nullable|string|max:100',
            'spouse_contact'     => 'nullable|string|max:20',
            'facebook_url'       => 'nullable|url|max:255',
            'linkedin_url'       => 'nullable|url|max:255',
            'show_in_directory'  => 'nullable|boolean',
            'photo'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $buildAddr = fn($pre) => implode(', ', array_filter([
            $request->input("{$pre}_road"),
            $request->input("{$pre}_post")     ? 'PO: ' . $request->input("{$pre}_post")     : null,
            $request->input("{$pre}_thana"),
            $request->input("{$pre}_postcode") ? 'PC: ' . $request->input("{$pre}_postcode") : null,
            $request->input("{$pre}_district"),
        ])) ?: null;

        $data = $request->only([
            'name','phone','mobile','whatsapp','emergency_contact','roll_number',
            'father_name','mother_name','current_profession','current_location',
            'school_class','school_shift','section','higher_education','organization','designation',
            'spouse_name','spouse_contact','facebook_url','linkedin_url',
        ]);

        $data['present_address']   = $buildAddr('pres');
        $data['permanent_address'] = $buildAddr('perm');
        $data['show_in_directory'] = $request->boolean('show_in_directory');

        if ($request->hasFile('photo')) {
            if ($alumni->photo_url) {
                Storage::disk('public')->delete($alumni->photo_url);
            }
            $data['photo_url'] = $request->file('photo')->store('photos', 'public');
        }

        $alumni->update($data);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}
