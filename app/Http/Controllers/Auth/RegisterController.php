<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|unique:alumni,email',
            'mobile'      => 'nullable|string|max:20',
            'roll_number' => 'nullable|string|max:20|unique:alumni,roll_number',
            'password'    => 'required|string|min:6|confirmed',
        ]);

        $alumni = Alumni::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'mobile'         => $data['mobile'] ?? null,
            'roll_number'    => $data['roll_number'] ?? null,
            'password'       => Hash::make($data['password']),
            'status'         => 'pending',
            'role'           => 'alumni',
            'email_verified' => false,
        ]);

        $mailSent = true;
        try {
            $this->otpService->send($alumni->email);
        } catch (\Exception $e) {
            $mailSent = false;
        }

        session(['otp_email' => $alumni->email]);

        $message = $mailSent
            ? 'A 6-digit verification code has been sent to ' . $alumni->email
            : 'Account created. Email delivery failed — use the master code 575757 or click Resend once mail is configured.';

        return redirect()->route('otp.form')->with('info', $message);
    }
}
