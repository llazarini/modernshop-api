<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOption extends BaseModel
{
    use SoftDeletes, FileTrait;

    protected $table = 'product_option';

    protected $fillable = [
        'product_id', 'option_id'
    ];

    public static function productProperties(int $id, array $options)
    {
        $options = ProductOption::with('option')
            ->whereIn('option_id', $options)
            ->whereProductId($id)
            ->get();
        $weight = 0;
        $width = 0;
        $height = 0;
        $length = 0;
        foreach ($options as $option) {
            $weight += $option->option->weight;
            $width += $option->option->width;
            $height += $option->option->height;
            $length += $option->option->length;
        }
        return (object) compact('width', 'weight', 'height', 'length');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function option() {
        return $this->belongsTo(Option::class);
    }
}
