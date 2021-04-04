<?php

namespace App\Models;

class UserType extends BaseModel
{
    protected $fillable = [
        'name', 'slug',
    ];

    public static function getId($slug)
    {
        $userType = UserType::whereSlug($slug)->first();
        return $userType ? $userType->id : null;
    }
}
