<?php

namespace App\Models;

class UserAddress extends BaseModel
{
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
}
