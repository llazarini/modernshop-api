<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends BaseModel
{
    use SoftDeletes, FileTrait;

    protected $fillable = [
        'name',
    ];
}
