<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\SmsRequestFailedException;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class AuthBySms extends Strategy
{
    public function request(Request $request, User $user)
    {
        Auth::logout();

        $this->writeToSession($request, $user);

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

        return redirect($this->redirectTokenPath());
    }
}
