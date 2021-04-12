<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BannerCategory extends BaseModel
{
    use SoftDeletes, FileTrait;

    public static $searchFields = ['name'];

    protected $fillable = [
        'slug', 'name',
    ];

    public function banners() {
        return $this->hasMany(Banner::class);
    }
}
