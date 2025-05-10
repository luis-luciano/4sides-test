<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;


class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request)
    {
        $verification_code = $request->get('verification_code');
        $email = $request->get('email');

        return view('auth.passwords.reset', compact('verification_code', 'email'));
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $response = $this->validateReset($request);

        if ($response instanceof User) {
            $this->resetPassword($response, $request->get('password'));
            return $this->sendResetResponse($request, Password::PASSWORD_RESET);
        }

        return $this->sendResetFailedResponse($request, $response);
    }

    protected function getUser(Request $request)
    {
        return User::where('usuario_email', $request->get('usuario_email'))->first();
    }
    /**
     * Validate a password reset for the given credentials.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    protected function validateReset(#[\SensitiveParameter] Request $request)
    {
        if (is_null($user = $this->getUser($request))) {
            return PasswordBroker::INVALID_USER;
        }

        $passwordResetService = new PasswordResetService($request->get('usuario_email'));

        if ($passwordResetService->invalidVerificationCode($request->get('verification_code'))) {
            return 'passwords.verify_code_invalid';
        }

        return $user;
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'usuario_email',
            'password',
            'password_confirmation'
        );
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'usuario_email' => 'required|email',
            'verification_code' => 'required|digits:6',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return redirect()->back()
            ->withInput($request->only('usuario_email'))
            ->withErrors(['usuario_email' => trans($response)]);
    }
}
