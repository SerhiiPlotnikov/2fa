<?php

namespace App\Http\Controllers;

use App\DiallingCode;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\RegistrationFailedException;
use App\Http\Requests\Update2FASettingsRequest;

use App\Services\AuthyAuthentication;
use App\Services\AuthyFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorSettingsController extends Controller
{
    private AuthyAuthentication $authy;

    public function __construct(AuthyAuthentication $authy)
    {
        $this->authy = $authy;
    }

    public function index()
    {
        return view('settings.twofactor', ['diallingCodes' => DiallingCode::all()]);
    }

    public function update(Update2FASettingsRequest $request)
    {
        $user = $request->user();
        $user->phoneNumber()->delete();

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

        if ($request->two_factor_type === 'app') {
            $user->two_factor_type = $request->get('two_factor_type');
            $user->save();
//
            $qrCode = $this->authy->generateQRCode($user->authy_id, 250, $user->email);
//            return redirect()->route('qr')->with(['qrCode' => $qrCode, 'twoFactorType' => $request->get('two_factor_type')]);
            return redirect()->back()->with(['qrCode' => $qrCode, 'twoFactorType' => $request->get('two_factor_type')]);

        }

        $user->two_factor_type = $request->get('two_factor_type');
        $user->save();

        return redirect()->route('home');
    }

    public function showQrCode(Request $request)
    {
        return view('auth.qr', ['qrUrl' => session()->get('qrCode'), 'twoFactorType' => session()->get('twoFactorType')]);
    }

}
