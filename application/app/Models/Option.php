<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends BaseModel
{
    use SoftDeletes, FileTrait;

    protected $fillable = [
        'name', 'description', 'price', 'type', 'weight', 'width', 'height', 'length'
    ];
}
