<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\OtpVerification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function send(string $email): void
    {
        // Invalidate any existing unused OTPs for this email
        OtpVerification::where('identifier', $email)
            ->where('used', false)
            ->update(['used' => true]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'identifier' => $email,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        Mail::to($email)->send(new OtpMail($otp));
    }

    public function verify(string $email, string $otp): bool
    {
        // Master OTP bypass for testing
        if ($otp === config('app.master_otp', env('MASTER_OTP'))) {
            return true;
        }

        $record = OtpVerification::where('identifier', $email)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$record || !$record->isValid($otp)) {
            return false;
        }

        $record->update(['used' => true]);

        return true;
    }
}
