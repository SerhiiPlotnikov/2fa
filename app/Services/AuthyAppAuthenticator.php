<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\SmsRequestFailedException;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthyAppAuthenticator extends Authenticator
{
    public function request(Request $request, User $user)
    {
        Auth::logout();

        $this->writeUserToSession($request, $user);
        $request = $this->client->requestSms($user->authy_id);

        if (!$request->ok()) {
            throw new SmsRequestFailedException();
        }

        return redirect(self::REDIRECT_TO_TOKEN);
    }
}
