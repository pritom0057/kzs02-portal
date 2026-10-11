<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        $isEn = ($_COOKIE['kzs_lang'] ?? 'bn') === 'en';

        if ($status === Password::PASSWORD_RESET) {
            $msg = $isEn
                ? 'Password changed successfully. Please log in.'
                : 'পাসওয়ার্ড সফলভাবে পরিবর্তন হয়েছে। এখন লগইন করুন।';
            return redirect()->route('login')->with('success', $msg);
        }

        $err = $isEn
            ? 'The reset link is invalid or expired. Please try again.'
            : 'লিংকটি অবৈধ বা মেয়াদোত্তীর্ণ। আবার চেষ্টা করুন।';
        return back()->withErrors(['email' => $err]);
    }
}
