<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\{
    InvalidTokenException,
    RegistrationFailedException,
    SmsRequestFailedException
};
use App\User;
use Authy\AuthyApi;
use Authy\AuthyFormatException;

class AuthyService
{
    private AuthyApi $client;

    public function __construct(AuthyApi $client)
    {
        $this->client = $client;
    }

    public function registerUser(User $user): int
    {

        $user = $this->client->registerUser(
            $user->email,
            $user->phoneNumber->phone_number,
            $user->phoneNumber->diallingCode->dialling_code
        );
        if (!$user->ok()) {
            throw new RegistrationFailedException();
        }

        return $user->id();
    }

    public function verifyToken(int $token, User $user = null): bool
    {
        try {
            $verification = $this->client->verifyToken(
                $user ? $user->authy_id : request()->session()->get('authy.authy_id'),
                $token
            );
        } catch (AuthyFormatException $exception) {
            throw new InvalidTokenException();
        }

        if (!$verification->ok()) {
            throw new InvalidTokenException();
        }

        return true;
    }


    public function requestSms(User $user): void
    {
        $request = $this->client->requestSms(
            $user->authy_id,
            [
                'force' => true
            ]
        );

        if (!$request->ok()) {
            throw new SmsRequestFailedException();
        }
    }
}
