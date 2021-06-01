<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends BaseModel
{
    use SoftDeletes, FileTrait;

    protected $fillable = [
        'name', 'attribute_id', 'description', 'price', 'type', 'weight', 'width', 'height', 'length'
    ];

    public function attribute() {
        return $this->belongsTo(Attribute::class);
    }
}
