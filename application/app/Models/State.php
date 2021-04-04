<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class State extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug'
    ];
}
