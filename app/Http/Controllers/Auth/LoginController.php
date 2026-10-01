<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string',
        ]);

        $id = trim($request->identifier);

        // Allow login via email, mobile, or roll number
        $alumni = Alumni::where('email', $id)
            ->orWhere('mobile', $id)
            ->orWhere(function ($q) use ($id) {
                $q->whereNotNull('roll_number')->where('roll_number', $id);
            })
            ->first();

        if (!$alumni || !Hash::check($request->password, $alumni->password)) {
            return back()->withErrors(['identifier' => 'Credentials do not match our records.'])->withInput();
        }

        if (!$alumni->email_verified) {
            $this->otpService->send($alumni->email);
            session(['otp_email' => $alumni->email]);
            return redirect()->route('otp.form')
                ->with('info', 'Please verify your email first. A new code has been sent.');
        }

        if ($alumni->status === 'rejected') {
            return back()->withErrors(['identifier' => 'Your account has been rejected. Please contact the reunion committee.']);
        }

        if ($alumni->status === 'pending') {
            return redirect()->route('pending');
        }

        Auth::login($alumni, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(
            $alumni->isAdmin() ? route('admin.dashboard') : route('dashboard')
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
