<?php
declare(strict_types=1);

namespace App\Services;

use App\User;
use Illuminate\Http\Request;

abstract class Authenticator
{
    protected const REDIRECT_TO_TOKEN = '/auth/token';

    protected AuthyApi $client;

    public function __construct(AuthyApi $client)
    {
        $this->client = $client;
    }

    protected function writeUserToSession(Request $request, User $user): void
    {
        $request->session()->put('authy', [
            'user_id' => $user->id,
            'authy_id' => $user->authy_id,
            'using_sms' => false,
            'remember' => $request->has('remember')
        ]);
    }

    abstract public function request(Request $request, User $user);
}
