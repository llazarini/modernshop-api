<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends BaseModel
{
    use SoftDeletes;

    protected $with = ['file'];

    public static $searchFields = ['name', 'content', 'order'];

    protected $fillable = [
        'name', 'content', 'order', 'banner_category_id'
    ];

    public function file() {
        return $this
            ->belongsTo(File::class, 'id', 'type_id')
            ->where('type', '=', Banner::class);
    }
}
