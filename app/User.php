<?php

declare(strict_types=1);

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable
{
    use Notifiable;

    public const TWO_FACTOR_AUTH_SMS_TYPE = 'sms';
    public const TWO_FACTOR_AUTH_GOOGLE_APP_TYPE = 'google';

    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_type',
        'authy_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = [
        'phoneNumber'
    ];

    public function phoneNumber(): HasOne
    {
        return $this->hasOne(PhoneNumber::class);
    }

    public function hasTwoFactorType(string $type): bool
    {
        return $this->two_factor_type === $type;
    }

    public function hasSmsTwoFactorAuthenticationEnabled(): bool
    {
        return $this->two_factor_type === self::TWO_FACTOR_AUTH_SMS_TYPE;
    }

    public function hasDiallingCode(int $diallingCodeId): bool
    {
        if ($this->phoneNumber()->exists()) {
            return $this->phoneNumber->diallingCode->id === $diallingCodeId;
        }

        return false;
    }

    public function hasPhoneNumber(): bool
    {
        return $this->whereHas(
            'phoneNumber',
            function (Builder $query) {
                $query->whereNotNull('phone_number');
            }
        )->exists();
    }

    public function getPhoneNumber(): ?string
    {
        if ($this->hasPhoneNumber()) {
            return $this->phoneNumber->phone_number;
        }
        return null;
    }

    public function registeredForTwoFactorAuthentication(): bool
    {
        return $this->authy_id !== null;
    }
}
