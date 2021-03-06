<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentStatus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug'
    ];

    public static function slug(string $slug)
    {
        $type = PaymentStatus::whereSlug($slug)
            ->first();
        return $type ? $type->id : null;
    }
}
