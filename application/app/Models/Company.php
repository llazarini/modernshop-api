<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug',
    ];
}
