<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\User\GetUserByIdAction;
use App\Actions\User\GetUserByIdRequest;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\SmsRequestFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyTokenHttpRequest;
use App\Services\AuthyAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthTokenController extends Controller
{
    private AuthyAuthentication $authy;
    private GetUserByIdAction $getUserByIdAction;

    public function __construct(AuthyAuthentication $authy, GetUserByIdAction $getUserByIdAction)
    {
        $this->authy = $authy;
        $this->getUserByIdAction = $getUserByIdAction;
    }

    public function getToken(Request $request)
    {
        if (!$request->session()->has('authy')) {
            return redirect()->route('home');
        }

        return view('auth.token');
    }

    public function verifyToken(VerifyTokenHttpRequest $request)
    {
        try {
            $this->authy->verifyToken($request->getToken());
        } catch (InvalidTokenException $exception) {
            return redirect()->back()->withErrors(
                [
                    'token' => $exception->getMessage()
                ]
            );
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

    public function getResendSMS(Request $request)
    {
        $user = $this->getUserByIdAction->execute(
            new GetUserByIdRequest($request->session()->get('authy.user_id'))
        );

        if (!$user->hasSmsTwoFactorAuthenticationEnabled()) {
            return redirect()->back();
        }
        try {
            $this->authy->request($request, $user);
        } catch (SmsRequestFailedException $exception) {
            return redirect()->back();
        }

        return redirect()->back();
    }
}
