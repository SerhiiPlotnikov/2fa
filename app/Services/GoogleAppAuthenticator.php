<?php
declare(strict_types=1);

namespace App\Services;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleAppAuthenticator extends Authenticator
{
    public function request(Request $request, User $user)
    {
        Auth::logout();

        $this->writeUserToSession($request, $user);
        return redirect(self::REDIRECT_TO_TOKEN);
    }
}
