<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class File extends BaseModel
{
    public function toArray() {
        $row = parent::toArray();
        $typeUrl = Str::slug($this->attributes['type']);
        $row['url'] = url(Storage::url("{$typeUrl}/{$this->attributes['name']}"));
        return $row;
    }
}
