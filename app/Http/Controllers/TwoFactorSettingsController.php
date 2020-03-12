<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\RegistrationFailedException;
use App\Http\Requests\Update2FASettingsHttpRequest;
use App\Services\AuthyAuthentication;
use App\Actions\Phones\GetDiallingCodes\GetDiallingCodesAction;
use App\Actions\Phones\UpdateUserByPhoneAction;
use Illuminate\Http\Request;
use App\Actions\Phones\UpdateUserByPhone\UpdateUserByPhoneRequest;

class TwoFactorSettingsController extends Controller
{
    private AuthyAuthentication $authy;
    private GetDiallingCodesAction $getDiallingCodesAction;
    private UpdateUserByPhoneAction $updateUserByPhoneAction;

    public function __construct(
        AuthyAuthentication $authy,
        GetDiallingCodesAction $getDiallingCodesAction,
        UpdateUserByPhoneAction $updateUserByPhoneAction
    ) {
        $this->authy = $authy;
        $this->getDiallingCodesAction = $getDiallingCodesAction;
        $this->updateUserByPhoneAction = $updateUserByPhoneAction;
    }

    public function index(Request $request)
    {
        return view(
            'settings.twofactor',
            [
                'diallingCodes' => $this->getDiallingCodesAction->execute(),
                'user' => $request->user(),
                'twoFactorTypes' => config('twofactor.types')
            ]
        );
    }

    public function update(Update2FASettingsHttpRequest $request)
    {
        $user = $request->user();

        try {
            $this->updateUserByPhoneAction->execute(
                new UpdateUserByPhoneRequest(
                    $user,
                    $request->getPhoneNumber(),
                    $request->getDiallingCode(),
                    $request->getAuthType()
                )
            );
        } catch (\Throwable $e) {
            return redirect()->back();
        }

        if ($request->getAuthType() === 'google') {
            $qrCode = $this->authy->generateQRCode($user->authy_id, 250, $user->email);
            return redirect()->back()->with(
                [
                    'qrCode' => $qrCode,
                    'twoFactorType' => $request->getAuthType()
                ]
            );
        }

        return redirect()->route('home');
    }

    public function showQrCode(Request $request)
    {
        return view(
            'auth.qr',
            [
                'qrUrl' => session()->get('qrCode'),
                'twoFactorType' => session()->get('twoFactorType')
            ]
        );
    }
}
