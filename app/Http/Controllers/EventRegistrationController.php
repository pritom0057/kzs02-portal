<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventRegistrationController extends Controller
{
    public function show()
    {
        $alumni       = auth()->user();
        $registration = $alumni->eventRegistration;

        return view('event.register', compact('alumni', 'registration'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'school_class'          => 'required|string|max:60',
            'school_shift'          => 'required|in:Morning,Day',
            'school_section'        => 'required|in:A,B',
            'name_bn'               => 'nullable|string|max:120',
            'mobile'                => 'required|string|max:20',
            'emergency_contact'     => 'required|string|max:20',
            'higher_education'      => 'required|string|max:200',
            'tshirt_size'           => 'required|in:S,M,L,XL,XXL,XXXL',
            'designation'           => 'required|string|max:150',
            'organization'          => 'required|string|max:150',
            'bring_spouse'          => 'nullable|boolean',
            'spouse_name'           => 'nullable|string|max:120',
            'spouse_name_bn'        => 'nullable|string|max:120',
            'children_details'      => 'nullable|array|max:10',
            'children_details.*.name'    => 'required_with:children_details|string|max:100',
            'children_details.*.name_bn' => 'nullable|string|max:100',
            'children_details.*.age_size'=> 'nullable|string|max:50',
            'guests_details'        => 'nullable|array|max:10',
            'guests_details.*.name'         => 'required_with:guests_details|string|max:100',
            'guests_details.*.relationship' => 'nullable|string|max:60',
            'guests_details.*.contact'      => 'nullable|string|max:20',
            'driver_included'       => 'nullable|boolean',
            'donation_amount'       => 'nullable|integer|min:0|max:1000000',
            'pres_road'             => 'required|string|max:200',
            'pres_post'             => 'required|string|max:100',
            'pres_thana'            => 'required|string|max:100',
            'pres_postcode'         => 'required|string|max:20',
            'pres_district'         => 'required|string|max:100',
            'perm_road'             => 'required|string|max:200',
            'perm_post'             => 'required|string|max:100',
            'perm_thana'            => 'required|string|max:100',
            'perm_postcode'         => 'required|string|max:20',
            'perm_district'         => 'required|string|max:100',
            'passport_photo'        => 'nullable|image|max:3072',
            'family_photo'          => 'nullable|image|max:5120',
        ]);

        $alumni = auth()->user();

        // Handle photo uploads
        $passportUrl    = $alumni->photo_url;
        $familyPhotoUrl = $alumni->family_photo_url;

        $uploadDir = public_path('uploads/photos');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('passport_photo')) {
            $file        = $request->file('passport_photo');
            $filename    = 'passport_' . $alumni->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $passportUrl = 'uploads/photos/' . $filename;
        }

        if ($request->hasFile('family_photo')) {
            $file           = $request->file('family_photo');
            $filename       = 'family_' . $alumni->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $familyPhotoUrl = 'uploads/photos/' . $filename;
        }

        // Build address strings
        $presentAddress  = implode(', ', array_filter([
            $request->pres_road,
            'PO: ' . $request->pres_post,
            $request->pres_thana,
            'PC: ' . $request->pres_postcode,
            $request->pres_district,
        ]));

        $permanentAddress = implode(', ', array_filter([
            $request->perm_road,
            'PO: ' . $request->perm_post,
            $request->perm_thana,
            'PC: ' . $request->perm_postcode,
            $request->perm_district,
        ]));

        // Save alumni profile fields (including spouse & children synced from registration)
        $alumni->update([
            'name_bn'           => $request->name_bn,
            'mobile'            => $request->mobile,
            'emergency_contact' => $request->emergency_contact,
            'school_class'      => $request->school_class,
            'school_shift'      => $request->school_shift,
            'section'           => $request->school_section,
            'higher_education'  => $request->higher_education,
            'designation'       => $request->designation,
            'organization'      => $request->organization,
            'present_address'   => $presentAddress,
            'permanent_address' => $permanentAddress,
            'photo_url'         => $passportUrl,
            'family_photo_url'  => $familyPhotoUrl,
            'spouse_name'       => $request->spouse_name,
        ]);

        // Calculate totals
        $bringSpouse    = $request->boolean('bring_spouse');
        $childrenDetails = $request->input('children_details', []);
        $childrenCount  = count(array_filter($childrenDetails, fn($c) => !empty($c['name'])));
        $guestsDetails  = $request->input('guests_details', []);
        $guestsCount    = count(array_filter($guestsDetails, fn($g) => !empty($g['name'])));
        $driverIncluded = $request->boolean('driver_included');
        $donationAmount = (int) ($request->input('donation_amount', 0));

        $total = EventRegistration::calculateTotal($bringSpouse, $childrenCount, $guestsCount, $driverIncluded, $donationAmount);

        // Save / update event registration
        EventRegistration::updateOrCreate(
            ['alumni_id' => $alumni->id],
            [
                'tshirt_size'      => $request->tshirt_size,
                'bring_spouse'     => $bringSpouse,
                'spouse_name'      => $request->spouse_name,
                'spouse_name_bn'   => $request->spouse_name_bn,
                'children_count'   => $childrenCount,
                'children_details' => $childrenCount > 0 ? array_values(array_filter($childrenDetails, fn($c) => !empty($c['name']))) : null,
                'guests_count'     => $guestsCount,
                'guests_details'   => $guestsCount > 0 ? array_values(array_filter($guestsDetails, fn($g) => !empty($g['name']))) : null,
                'driver_included'  => $driverIncluded,
                'donation_amount'  => $donationAmount,
                'guest_count'      => ($bringSpouse ? 1 : 0) + $childrenCount + $guestsCount + ($driverIncluded ? 1 : 0),
                'total_amount'     => $total,
            ]
        );

        return redirect()->route('event.show')
            ->with('success', 'Registration saved! Proceed to payment below. Total: ৳' . number_format($total, 0));
    }
}
