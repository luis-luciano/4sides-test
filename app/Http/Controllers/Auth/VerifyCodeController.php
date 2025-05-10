<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\PasswordResetService;
use Illuminate\Support\Facades\DB;

class VerifyCodeController extends Controller
{
    public function showVerifyCodeForm(Request $request)
    {
        $email = $request->query('email');
        $verification_code = $request->query('verification_code');

        return view('auth.passwords.verify_code', compact('email', 'verification_code'));
    }

    public function sendVerifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|digits:6',
        ]);

        $passwordResetService = new PasswordResetService($request->email);

        if ($passwordResetService->expiredVerificationCode()) {
            return redirect()->back()->withInput($request->only('email', 'verification_code'))->withErrors([trans('passwords.verify_code_expired')]);
        }

        return redirect()->route('password.reset.verify-code', ['email' => $request->email, 'verification_code' => $request->verification_code]);
    }
}
