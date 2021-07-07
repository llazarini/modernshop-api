<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;

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
            $height = 200;
        }
        $size = "{$width}x{$height}";
        $manager = new ImageManager(array('driver' => 'gd'));
        if (Storage::exists("public/{$type}/{$size}/{$image}")) {
            return response()->redirectTo(Storage::url("public/{$type}/{$size}/{$image}"), 301);
        } else if (!Storage::exists("public/{$type}/{$image}")) {
            return response()->redirectTo(public_path('default.png'));
        }
        $make = $manager->make(Storage::get("public/{$type}/{$image}"));
        $make->fit($width, $height);
        if (!Storage::makeDirectory("public/{$type}/{$size}")) {
            return response()->redirectTo(public_path('default.png'));
        }
        $make->save(storage_path("app/public/{$type}/{$size}/{$image}"), 85, 'jpg');
        return response()->redirectTo(Storage::url("public/{$type}/{$size}/{$image}"), 301);
    }
}
