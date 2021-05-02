<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends BaseModel
{
    use SoftDeletes, FileTrait;

    protected $fillable = [
        'name'
    ];

    public static function slug(string $slug)
    {
        $type = PaymentType::whereSlug($slug)
            ->first();
        return $type ? $type->id : null;
    }
}
