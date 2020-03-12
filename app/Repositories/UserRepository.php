<?php

declare(strict_types=1);

namespace App\Repositories;

use App\User;

class UserRepository
{
    public function updateAuth(User $user, string $twoFactorType, int $authyId): void
    {
        $user->update(
            [
                'authy_id' => $authyId,
                'two_factor_type' => $twoFactorType
            ]
        );
    }

    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }
}
