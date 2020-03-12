<?php
declare(strict_types=1);

namespace App\Actions\Phones\GetDiallingCodes;

use App\Repositories\DiallingCodeRepository;
use Illuminate\Support\Collection;

class GetDiallingCodesAction
{
    private DiallingCodeRepository $diallingCodeRepository;

    public function __construct(DiallingCodeRepository $diallingCodeRepository)
    {
        $this->diallingCodeRepository = $diallingCodeRepository;
    }

    public function execute(): Collection
    {
        return $this->diallingCodeRepository->getAll();
    }
}
