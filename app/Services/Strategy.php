<?php
declare(strict_types=1);

namespace App\Services;

use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

abstract class Strategy
{
    use AuthenticatesUsers;

    protected AuthyApi $client;
    protected string $redirectToToken = '/auth/token';

    public function __construct(AuthyApi $client)
    {
        $this->client = $client;
    }

    public abstract function request(Request $request, User $user);

    protected function writeToSession(Request $request, User $user): void
    {
        $request->session()->put('authy', [
            'user_id' => $user->id,
            'authy_id' => $user->authy_id,
            'using_sms' => false,
            'remember' => $request->has('remember')
        ]);
    }

    protected function redirectTokenPath()
    {
        return $this->redirectToToken;
    }
}
