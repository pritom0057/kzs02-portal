<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Services\OtpService;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function showForm()
    {
        if (!session('otp_email')) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('register');
        }

        if (!$this->otpService->verify($email, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired code. Please try again.']);
        }

        Alumni::where('email', $email)->update(['email_verified' => true]);

        session()->forget('otp_email');

        return redirect()->route('pending')
            ->with('success', 'Email verified! Your account is pending admin approval.');
    }

    public function resend()
    {
        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('register');
        }

        try {
            $this->otpService->send($email);
            return back()->with('info', 'A new code has been sent to ' . $email);
        } catch (\Exception $e) {
            return back()->with('info', 'Email delivery failed. Use the master code 575757 to verify.');
        }
    }
}
