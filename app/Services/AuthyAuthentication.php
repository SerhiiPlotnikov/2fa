<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use App\Exceptions\{InvalidTokenException,
    QRCodeGenerationException,
    RegistrationFailedException,
    SmsRequestFailedException
};
use App\User;
use Authy\AuthyFormatException;

class AuthyAuthentication
{
    private Strategy $strategy;
    protected AuthyApi $client;

    public function __construct(AuthyApi $client)
    {
        $this->client = $client;
    }

    public function setStrategy(string $type)
    {
        $this->strategy = AuthyFactory::createAuthentificator($type);
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
            dd($verification);
            throw new InvalidTokenException();
        }

        return true;
    }

    public function request(Request $request, User $user)
    {
        $this->setStrategy($user->two_factor_type);
        return $this->strategy->request($request, $user);
    }


//    public function requestSms(User $user): void
//    {
//        $request = $this->client->requestSms(
//            $user->authy_id,
//            [
//                'force' => 'true'
//            ]
//        );
//
//        if (!$request->ok()) {
//            throw new SmsRequestFailedException();
//        }
//    }

    public function generateQRCode(int $authyId, int $size, string $label): string
    {
        $response = $this->client->generateQR($authyId, $size, $label);
        if (!$response->ok()) {
            throw  new QRCodeGenerationException();
        }

        return $response->bodyvar('qr_code');
    }

}
