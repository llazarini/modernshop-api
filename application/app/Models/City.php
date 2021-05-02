<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class City extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'state_id'
    ];

    public function state() {
        return $this->belongsTo(State::class);
    }
}
