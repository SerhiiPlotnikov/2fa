<?php
declare(strict_types=1);

namespace App\Services;

use App\User;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;

class DisableAuth extends Strategy
{
    use RedirectsUsers;

    public function request(Request $request, User $user)
    {
        return redirect()->intended($this->redirectPath());
    }
}
