<?php

declare(strict_types=1);

namespace App\Repositories;

use App\User;

class PhoneNumberRepository
{
    public function updateByUser(User $user, ?string $phoneNumber, ?string $diallingCode): void
    {
        if ($user->phoneNumber()->exists()) {
            $user->phoneNumber()->update(
                [
                    'phone_number' => $phoneNumber,
                    'dialling_code_id' => $diallingCode
                ]
            );
            return;
        }
        $user->phoneNumber()->create(
            [
                'phone_number' => $phoneNumber,
                'dialling_code_id' => $diallingCode
            ]
        );
    }
}
