<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class Update2FASettingsHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'two_factor_type' => [
                'required',
                'in:' . implode(',', array_keys(config('twofactor.types')))
            ],
            'phone_number' => [
                'required_unless:two_factor_type,off'
            ],
            'phone_number_dialling_code' => [
                'required_unless:two_factor_type,off'
            ]
        ];
    }

    public function getPhoneNumber(): string
    {
        return $this->get('phone_number');
    }

    public function getDiallingCode(): string
    {
        return $this->get('phone_number_dialling_code');
    }

    public function getAuthType(): string
    {
        return $this->get('two_factor_type');
    }

    protected function getValidatorInstance(): Validator
    {
        $validator = parent::getValidatorInstance();

        $validator->sometimes(
            'phone_number_dialling_code',
            'exists:dialling_codes,id',
            function ($input) {
                return $input->two_factor_type !== 'off';
            }
        );

        return $validator;
    }
}
