<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidTokenException;
use App\Exceptions\SmsRequestFailedException;
use App\Http\Controllers\Controller;
use App\Services\AuthyAuthentication;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthTokenController extends Controller
{
    private AuthyAuthentication $authy;

    public function __construct(AuthyAuthentication $authy)
    {
        $this->authy = $authy;
    }

    public function getToken(Request $request)
    {

        dd(!$request->session()->has('authy'));
        if (!$request->session()->has('authy')) {
            return redirect()->route('home');
        }
        return view('auth.token');
    }

    public function postToken(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
        if ($request->get('two-factor-type') === 'app') {
            try {
                $verification = $this->authy->verifyToken($request->get('token'));
//                $user = $request->user();
//                $user->save();
            } catch (InvalidTokenException $exception) {

                return redirect()->back()->withErrors([
                    'token' => 'Invalid token'
                ]);
            }
        } else {
            try {
                $verification = $this->authy->verifyToken($request->get('token'));
            } catch (InvalidTokenException $exception) {
                return redirect()->back()->withErrors([
                    'token' => 'Invalid token'
                ]);
            }
        }

        if (Auth::loginUsingId(
            $request->session()->get('authy.user_id'),
            $request->session()->get('authy.remember')
        )) {
            $request->session()->forget('authy');
            return redirect()->intended();
        }

        return redirect()->route('home');
    }

    public function getResend(Request $request)
    {
        $user = User::findOrFail($request->session()->get('authy.user_id'));
        if (!$user->hasSmsTwoFactorAuthenticationEnabled()) {
            return redirect()->back();
        }

        try {
            $this->authy->requestSms($user);
        } catch (SmsRequestFailedException $exception) {
            return redirect()->back();
        }

        return redirect()->back();
    }
}
