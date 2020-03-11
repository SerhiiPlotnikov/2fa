<?php
declare(strict_types=1);

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function phoneNumber()
    {
        return $this->hasOne(PhoneNumber::class);
    }

    public function hasTwoFactorAuthenticationEnabled(): bool
    {
        return $this->two_factor_type !== 'off';
    }

    public function hasSmsTwoFactorAuthenticationEnabled(): bool
    {
        return $this->two_factor_type === 'sms';
    }

    public function hasGoogleTwoFactorAuthenticationEnabled(): bool
    {
        return $this->two_factor_type === 'app';
    }

    //+
    public function hasTwoFactorType(string $type): bool
    {
        return $this->two_factor_type === $type;
    }
//+
    public function hasDiallingCode(int $diallingCodeId): bool
    {
        if ($this->hasPhoneNumber()) {
            return $this->phoneNumber->diallingCode->id === $diallingCodeId;
        }
        return false;
    }
//+
    public function hasPhoneNumber()
    {
        return $this->whereHas('phoneNumber', function (Builder $query) {
           $query->whereNotNull('phone_number');
        })->exists();
//        return $this->phoneNumber && $this->phoneNumber->phone_number !== null;
    }

    public function getPhoneNumber(): string
    {
        if ($this->hasPhoneNumber()) {
            return $this->phoneNumber->phone_number;
        }
        return '';
    }

    public function registeredForTwoFactorAuthentication(): bool
    {
        return $this->authy_id !== null;
    }
}
