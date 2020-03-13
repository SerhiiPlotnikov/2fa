<?php

declare(strict_types=1);

namespace App\Actions\Phones\UpdateUserByPhone;

final class UpdateUserByPhoneResponse
{
    private ?string $qrCode;

    public function __construct(?string $qrCode)
    {
        $this->qrCode = $qrCode;
    }

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }
}