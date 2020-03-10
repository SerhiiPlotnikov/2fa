<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\SmsRequestFailedException;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthByAuthyApp extends Strategy
{
    public function request(Request $request, User $user)
    {
        $this->guard()->logout();
        $this->writeToSession($request, $user);
        $request = $this->client->requestSms($user->authy_id,);

        dd($request);
        if (!$request->ok()) {
            throw new SmsRequestFailedException();
        }

        return redirect()->route('get-token');
//        return redirect($this->redirectTokenPath());
    }
}
