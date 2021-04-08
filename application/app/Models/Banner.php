<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends BaseModel
{
    use SoftDeletes, FileTrait;

    public static $searchFields = ['name', 'content', 'order'];

    protected $fillable = [
        'name', 'content', 'order'
    ];
}
