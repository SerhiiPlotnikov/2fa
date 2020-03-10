<?php
declare(strict_types=1);

namespace App\Services;

class AuthyFactory
{
    public static function createAuthentificator(string $type): Strategy
    {
        switch ($type) {
            case 'sms':
                return new AuthBySms(new AuthyApi(env('AUTHY_SECRET_KEY')));
            case 'off':
                return new DisableAuth(new AuthyApi(env('AUTHY_SECRET_KEY')));
            case 'app':
                return new AuthByAuthyApp(new AuthyApi(env('AUTHY_SECRET_KEY')));
        }
    }
}
