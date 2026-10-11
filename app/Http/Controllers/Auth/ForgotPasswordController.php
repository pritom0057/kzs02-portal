<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        $isEn = ($_COOKIE['kzs_lang'] ?? 'bn') === 'en';

        if ($status === Password::RESET_LINK_SENT) {
            $msg = $isEn
                ? 'A password reset link has been sent to your email.'
                : 'আপনার ইমেইলে একটি পাসওয়ার্ড রিসেট লিংক পাঠানো হয়েছে।';
            return back()->with('success', $msg);
        }

        $err = $isEn
            ? 'No account found with this email.'
            : 'এই ইমেইলে কোনো অ্যাকাউন্ট পাওয়া যায়নি।';
        return back()->withErrors(['email' => $err]);
    }
}
