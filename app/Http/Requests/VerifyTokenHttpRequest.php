<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyTokenHttpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required'
        ];
    }

    public function getToken(): string
    {
        return $this->get('token');
    }
}
