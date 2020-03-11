<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\SmsRequestFailedException;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsAuthenticator extends Authenticator
{
    public function request(Request $request, User $user)
    {
        Auth::logout();

        $this->writeUserToSession($request, $user);

        try {
            $response = $this->client->requestSms(
                $user->authy_id,
                [
                    'force' => 'true'
                ]
            );
            if (!$response->ok()) {
                throw new SmsRequestFailedException();
            }
        } catch (SmsRequestFailedException $exception) {
            return redirect()->back();
        }

        $request->session()->push('authy.using_sms', true);

        return redirect(self::REDIRECT_TO_TOKEN);
    }
}
