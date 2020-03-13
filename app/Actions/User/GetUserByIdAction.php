<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Repositories\UserRepository;
use App\User;

class GetUserByIdAction
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(GetUserByIdRequest $request): User
    {
        return $this->userRepository->getUserById($request->getId());
    }
}