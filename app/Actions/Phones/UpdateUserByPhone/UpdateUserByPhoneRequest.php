<?php

declare(strict_types=1);

namespace App\Actions\Phones\UpdateUserByPhone;

use App\User;

class UpdateUserByPhoneRequest
{
    private User $user;
    private string $phoneNumber;
    private string $diallingCode;
    private string $authType;

    public function __construct(User $user, string $phoneNumber, string $diallingCode, string $authType)
    {
        $this->user = $user;
        $this->phoneNumber = $phoneNumber;
        $this->diallingCode = $diallingCode;
        $this->authType = $authType;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getDiallingCode(): string
    {
        return $this->diallingCode;
    }

    public function getAuthType(): string
    {
        return $this->authType;
    }
}
