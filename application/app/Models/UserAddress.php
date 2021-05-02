<?php

namespace App\Models;

class UserAddress extends BaseModel
{
    protected $with = ['city'];

    protected $fillable = [
        'user_id',
        'state_id',
        'city_id',
        'zip_code',
        'street_name',
        'street_number',
        'neighborhood',
        'complement'
    ];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function state() {
        return $this->belongsTo(State::class);
    }
}
