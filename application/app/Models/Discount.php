<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'code', 'value', 'type'
    ];
}
