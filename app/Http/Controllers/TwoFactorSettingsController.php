<?php

namespace App\Http\Controllers;

use App\DiallingCode;
use App\Exceptions\RegistrationFailedException;
use App\Services\AuthyService;
use Illuminate\Http\Request;

class TwoFactorSettingsController extends Controller
{
    private AuthyService $authy;

    public function __construct(AuthyService $authy)
    {
        $this->authy = $authy;
    }

    public function index()
    {
        return view('settings.twofactor', ['diallingCodes' => DiallingCode::all()]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'two_factor_type' => 'required|in:' . implode(',', array_keys(config('twofactor.types'))),
            'phone_number' => 'required_unless:two_factor_type,off',
            'phone_number_dialling_code' => 'required_unless:two_factor_type,off|exists:dialling_codes,id'
        ]);

        $user = $request->user();
        $user->phoneNumber()->delete();
        if (!$request->get('phone_number')) {
            return;
        }
        $user->phoneNumber()->create([
            'phone_number' => $request->get('phone_number'),
            'dialling_code_id' => $request->get('phone_number_dialling_code')
        ]);

        if (!$user->registeredForTwoFactorAuthentication()) {
            try {
                $authyId = $this->authy->registerUser($user);
                $user->authy_id = $authyId;
            } catch (RegistrationFailedException $exception) {
                return redirect()->back();
            }
        }

        $user->two_factor_type = $request->get('two_factor_type');
        $user->save();
        return redirect()->back();
    }
}
