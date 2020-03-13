<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\RegistrationFailedException;
use App\Http\Requests\Update2FASettingsHttpRequest;
use App\Services\AuthyAuthentication;
use App\Actions\Phones\GetDiallingCodes\GetDiallingCodesAction;
use App\Actions\Phones\UpdateUserByPhoneAction;
use App\User;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;
use App\Actions\Phones\UpdateUserByPhone\UpdateUserByPhoneRequest;

final class TwoFactorSettingsController extends Controller
{
    private AuthyAuthentication $authy;
    private GetDiallingCodesAction $getDiallingCodesAction;
    private UpdateUserByPhoneAction $updateUserByPhoneAction;
    private Config $config;

    public function __construct(
        AuthyAuthentication $authy,
        GetDiallingCodesAction $getDiallingCodesAction,
        UpdateUserByPhoneAction $updateUserByPhoneAction,
        Config $config
    ) {
        $this->authy = $authy;
        $this->getDiallingCodesAction = $getDiallingCodesAction;
        $this->updateUserByPhoneAction = $updateUserByPhoneAction;
        $this->config = $config;
    }

    public function index(Request $request)
    {
        return view(
            'settings.twofactor',
            [
                'diallingCodes' => $this->getDiallingCodesAction->execute(),
                'user' => $request->user(),
                'twoFactorTypes' => $this->config->get('twofactor.types')
            ]
        );
    }

    public function update(Update2FASettingsHttpRequest $request)
    {
        $user = $request->user();

        try {
            $response = $this->updateUserByPhoneAction->execute(
                new UpdateUserByPhoneRequest(
                    $user,
                    $request->getPhoneNumber(),
                    $request->getDiallingCode(),
                    $request->getAuthType()
                )
            );
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        if ($qrCode = $response->getQrCode()) {
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
