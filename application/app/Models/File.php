<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class File extends BaseModel
{
    public function toArray() {
        $row = parent::toArray();
        $row['url'] = url(Storage::url("{$this->attributes['type']}/{$this->attributes['name']}"));
        return $row;
    }
}
