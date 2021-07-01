<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends BaseModel
{
    use SoftDeletes, FileTrait;

    public static $searchFields = ['name'];

    protected $fillable = [
        'sku', 'name', 'description', 'meta_name', 'meta_description', 'meta_keys', 'stock', 'price', 'price_cost'
    ];

    public function categories() {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function options() {
        return $this->belongsToMany(Option::class, 'product_option');
    }

    public function attributes() {
        $attributes = Attribute::whereIn('id', $this->options->pluck('attribute_id'))
            ->get();
        foreach($attributes as $attribute) {
            $attribute->options = Option::whereIn('id', $this->options->where('attribute_id', '=', $attribute->id)->pluck('id'))->get();
        }
        return $attributes;
    }

    public function toArray() {
        $row = parent::toArray();
        $row['slug'] = Str::slug($this->attributes['name']);
        $row['url'] = sprintf('%s/product/view/%s/%s', env('APP_STORE_URL'), $row['id'], $row['slug']);
        return $row;
    }
}
