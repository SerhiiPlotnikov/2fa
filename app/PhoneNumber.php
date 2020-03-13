<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PhoneNumber extends Model
{
    protected $fillable = [
        'phone_number',
        'dialling_code_id'
    ];

    public function diallingCode(): BelongsTo
    {
        return $this->belongsTo(DiallingCode::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
