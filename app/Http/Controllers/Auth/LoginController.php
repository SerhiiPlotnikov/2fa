<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\AuthyAuthentication;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected string $redirectTo = RouteServiceProvider::HOME;
    private AuthyAuthentication $authy;

    public function __construct(AuthyAuthentication $authy)
    {
        $this->middleware('guest')->except('logout');
        $this->authy = $authy;
    }

    protected function authenticated(Request $request, User $user)
    {
        return $this->authy->request($request, $user);
    }
}
