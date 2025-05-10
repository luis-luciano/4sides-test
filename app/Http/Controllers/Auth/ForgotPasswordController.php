<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMailable;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $user = User::where('usuario_email', $request->email)->first();

        if (empty($user)) {
            return $this->sendResetLinkFailedResponse($request, PasswordBroker::INVALID_USER);
        }

        $passwordResetService = new PasswordResetService($request->get('email'));

        $passwordResetService->generateVerificationCode();

        try {
            Mail::to($request->email)->send(new PasswordResetMailable($passwordResetService->getPasswordResetUser()));
        } catch (\Exception $e) {
            return $this->sendResetLinkFailedResponse($request, $e->getMessage());
        }

        return $this->sendResetLinkResponse($request, PasswordBroker::RESET_LINK_SENT);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $request->wantsJson()
            ? view('', ['message' => trans($response)], 200)
            : back()->with('status', trans($response));
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return ['usuario_email' => $request->get('email')];
    }
}
