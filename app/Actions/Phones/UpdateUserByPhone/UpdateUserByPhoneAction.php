<?php

declare(strict_types=1);

namespace App\Actions\Phones;

use App\Actions\Phones\UpdateUserByPhone\UpdateUserByPhoneRequest;
use App\Actions\Phones\UpdateUserByPhone\UpdateUserByPhoneResponse;
use App\Exceptions\RegistrationFailedException;
use App\Repositories\PhoneNumberRepository;
use App\Repositories\UserRepository;
use App\Services\AuthyAuthentication;
use App\User;
use Illuminate\Database\DatabaseManager;

final class UpdateUserByPhoneAction
{
    private PhoneNumberRepository $phoneNumberRepository;
    private UserRepository $userRepository;
    private DatabaseManager $databaseManager;
    private AuthyAuthentication $authy;

    public function __construct(
        PhoneNumberRepository $phoneNumberRepository,
        UserRepository $userRepository,
        DatabaseManager $databaseManager,
        AuthyAuthentication $authy
    ) {
        $this->phoneNumberRepository = $phoneNumberRepository;
        $this->userRepository = $userRepository;
        $this->databaseManager = $databaseManager;
        $this->authy = $authy;
    }

    public function execute(UpdateUserByPhoneRequest $request): UpdateUserByPhoneResponse
    {
        $this->databaseManager->transaction(
            function () use ($request) {
                $this->phoneNumberRepository->updateByUser(
                    $request->getUser(),
                    $request->getPhoneNumber(),
                    $request->getDiallingCode()
                );

                if (!$request->getUser()->registeredForTwoFactorAuthentication()) {
                    $authyId = $this->authy->registerUser($request->getUser());
                } else {
                    ///remove user and create it again
                }

                if (!isset($authyId)) {
                    $authyId = (int)$request->getUser()->authy_id;
                }

                $this->userRepository->updateAuth($request->getUser(), $request->getAuthType(), $authyId);
            }
        );
        $qrCode = null;
        if ($request->getAuthType() === User::TWO_FACTOR_AUTH_GOOGLE_APP_TYPE) {
            $qrCode = $this->authy->generateQRCode(
                $request->getUser()->authy_id,
                250,
                $request->getUser()->email

            );
            return new UpdateUserByPhoneResponse($qrCode);
        }
        return new UpdateUserByPhoneResponse($qrCode);
    }
}
