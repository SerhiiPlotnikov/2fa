<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    protected $fillable = [
        'phone_number',
        'dialling_code_id'
    ];

    public function diallingCode()
    {
        return $this->belongsTo(DiallingCode::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
