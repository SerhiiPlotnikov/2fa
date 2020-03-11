<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DiallingCode;
use Illuminate\Support\Collection;

class DiallingCodeRepository
{
    public function getAll(): Collection
    {
        return DiallingCode::all();
    }
}
