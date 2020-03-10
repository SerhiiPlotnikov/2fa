<?php
declare(strict_types=1);

namespace App\Services;

use Authy\AuthyResponse;

class AuthyApi extends \Authy\AuthyApi
{
    public function generateQR(int $authyId, int $size, string $label): AuthyResponse
    {
        $authyId = urlencode((string)$authyId);

        $resp = $this->rest->post("protected/json/users/{$authyId}/secret", array_merge(
            $this->default_options,
            [
                'query' => [
                    "qr_size" => $size,
                    "label" => $label,
                ]
            ]
        ));

        return new AuthyResponse($resp);
    }
}
