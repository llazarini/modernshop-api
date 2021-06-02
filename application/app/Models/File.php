<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class File extends BaseModel
{
    public function toArray() {
        $row = parent::toArray();
        $typeUrl = Str::slug($this->attributes['type']);
        $row['url'] = url(sprintf('image?type=%s&image=%s',$typeUrl, $this->attributes['name']));
        return $row;
    }
}
