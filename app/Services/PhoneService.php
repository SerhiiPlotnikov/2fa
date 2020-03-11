<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\DiallingCodeRepository;
use Illuminate\Support\Collection;

class PhoneService
{
    private DiallingCodeRepository $diallingCodeRepository;

    public function __construct(DiallingCodeRepository $diallingCodeRepository)
    {
        $this->diallingCodeRepository = $diallingCodeRepository;
    }

    public function getDiallingCodes(): Collection
    {
        return $this->diallingCodeRepository->getAll();
    }
}
