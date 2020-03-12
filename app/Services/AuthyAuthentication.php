<?php

declare(strict_types=1);

namespace App\Services;

use App\Factories\AuthyFactory;
use Illuminate\Http\Request;
use App\Exceptions\{InvalidTokenException,
    QRCodeGenerationException,
    RegistrationFailedException,
};
use App\User;
use Authy\AuthyFormatException;

class AuthyAuthentication
{
    protected AuthyApi $client;
    private Authenticator $authenticator;
    private AuthyFactory $authyFactory;

    public function __construct(AuthyApi $client, AuthyFactory $authyFactory)
    {
        $this->client = $client;
        $this->authyFactory = $authyFactory;
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

    public function verifyToken(string $token, User $user = null): bool
    {
        try {
            $verification = $this->client->verifyToken(
                $user ? $user->authy_id : request()->session()->get('authy.authy_id'),
                $token
            );
        } catch (AuthyFormatException $exception) {
            throw new InvalidTokenException($exception->getMessage());
        }

        if (!$verification->ok()) {
            throw new InvalidTokenException($verification->bodyvar('message'));
        }

        return true;
    }

    public function request(Request $request, User $user)
    {
        $this->setAuthenticator($user->two_factor_type);
        return $this->authenticator->request($request, $user);
    }

    public function generateQRCode(int $authyId, int $size, string $label): string
    {
        $response = $this->client->generateQR($authyId, $size, $label);
        if (!$response->ok()) {
            throw  new QRCodeGenerationException();
        }

        return $response->bodyvar('qr_code');
    }

    private function setAuthenticator(string $type): void
    {
        $this->authenticator = $this->authyFactory->createAuthenticator($type);
    }
}
