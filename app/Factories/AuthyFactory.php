<?php
declare(strict_types=1);

namespace App\Factories;

use App\Services\Authenticator;
use App\Services\AuthyApi;
use App\Services\AuthyAppAuthenticator;
use App\Services\DisableAuthenticator;
use App\Services\GoogleAppAuthenticator;
use App\Services\SmsAuthenticator;

class AuthyFactory
{
    public function createAuthenticator(string $type): Authenticator
    {
        switch ($type) {
            case 'sms':
                return new SmsAuthenticator(new AuthyApi(env('AUTHY_SECRET_KEY')));
            case 'off':
                return new DisableAuthenticator(new AuthyApi(env('AUTHY_SECRET_KEY')));
            case 'authy':
                return new AuthyAppAuthenticator(new AuthyApi(env('AUTHY_SECRET_KEY')));
            case 'google':
                return new GoogleAppAuthenticator(new AuthyApi(env('AUTHY_SECRET_KEY')));
        }
    }
}
