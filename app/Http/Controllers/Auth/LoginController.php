<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\SmsRequestFailedException;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\AuthyAuthentication;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected string $redirectTo = RouteServiceProvider::HOME;
    protected string $redirectToToken = '/auth/token';

    private AuthyAuthentication $authy;

    public function __construct(AuthyAuthentication $authy)
    {
        $this->middleware('guest')->except('logout');
        $this->authy = $authy;
    }


    protected function authenticated(Request $request, User $user)
    {
        return $this->authy->request($request, $user);
//        if ($user->hasTwoFactorAuthenticationEnabled()) {
//            return $this->logoutAndRedirectToTokenEntry($request, $user);
//        }

//        return redirect()->intended($this->redirectPath());
    }

    protected function logoutAndRedirectToTokenEntry(Request $request, User $user)
    {
        $this->guard()->logout();

        $request->session()->put('authy', [
            'user_id' => $user->id,
            'authy_id' => $user->authy_id,
            'using_sms' => false,
            'remember' => $request->has('remember')
        ]);
//
        if ($user->hasSmsTwoFactorAuthenticationEnabled()) {
            try {
                $this->authy->requestSms($user);
            } catch (SmsRequestFailedException $exception) {
                return redirect()->back();
            }
//
            $request->session()->push('authy.using_sms', true);
        };

        return redirect($this->redirectTokenPath());
    }

    protected function redirectTokenPath()
    {
        return $this->redirectToToken;
    }
}
