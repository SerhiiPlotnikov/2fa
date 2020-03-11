<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\RegistrationFailedException;
use App\Http\Requests\Update2FASettingsRequest;
use App\Services\AuthyAuthentication;
use App\Services\PhoneService;
use Illuminate\Http\Request;

class TwoFactorSettingsController extends Controller
{
    private AuthyAuthentication $authy;
    private PhoneService $phoneService;

    public function __construct(AuthyAuthentication $authy, PhoneService $phoneService)
    {
        $this->authy = $authy;
        $this->phoneService = $phoneService;
    }

    public function index(Request $request)
    {
        return view('settings.twofactor', [
            'diallingCodes' => $this->phoneService->getDiallingCodes(),
            'user' => $request->user(),
            'twoFactorTypes' => config('twofactor.types')
        ]);
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

        if ($request->two_factor_type === 'google') {
            $user->two_factor_type = $request->get('two_factor_type');
            $user->save();

            $qrCode = $this->authy->generateQRCode($user->authy_id, 250, $user->email);
            return redirect()->back()->with(['qrCode' => $qrCode, 'twoFactorType' => $request->get('two_factor_type')]);
        }

        $user->two_factor_type = $request->get('two_factor_type');
        $user->save();

        return redirect()->route('home');
    }

    public function showQrCode(Request $request)
    {
        return view('auth.qr', [
            'qrUrl' => session()->get('qrCode'),
            'twoFactorType' => session()->get('twoFactorType')
        ]);
    }
}
