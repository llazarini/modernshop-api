<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    public function image(Request $request)
    {
        $type = $request->get('type');
        $image = $request->get('image');
        $width = $request->get('width');
        $height = $request->get('height');
        if (!$width && !$height) {
            $width = 200;
            $height = null;
        }
        $size = "{$width}x{$height}";
        Image::configure(array('driver' => 'imagick'));
        if (Storage::exists("public/{$type}/{$size}/{$image}")) {
            return response()->redirectTo(Storage::url("public/{$type}/{$size}/{$image}"));
        } else if (!Storage::exists("public/{$type}/{$image}")) {
            return response()->redirectTo(public_path('default.png'));
        }
        $make = Image::make(Storage::get("public/{$type}/{$image}"));
        $make->fit($width, $height);
        if (!Storage::makeDirectory("public/{$type}/{$size}")) {
            return response()->redirectTo(public_path('default.png'));
        }
        $make->save(storage_path("app/public/{$type}/{$size}/{$image}"), 85, 'jpg');
        return response()->redirectTo(Storage::url("public/{$type}/{$size}/{$image}"));
    }
}
