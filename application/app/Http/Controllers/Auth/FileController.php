<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileDeleteRequest;
use App\Http\Requests\FileStoreRequest;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{
    public function store(FileStoreRequest $request)
    {
        $requestFile = $request->file('file');
        $file = new File();
        $file->company_id = $request->user()->company_id;
        $file->request_token = $request->get('request_token');
        $file->name = explode('.', $requestFile->hashName())[0].'.jpg';
        $file->original_name = $requestFile->getFilename();
        $file->size = $requestFile->getSize();
        $file->type = $request->get('type');
        $file->type_id = $request->get('type_id');
        $typeUrl = Str::slug($file->type);
        if (extension_loaded('imagick')) {
            Image::configure(array('driver' => 'imagick'));
        }
        $image = Image::make($requestFile);
        if(!$file->save() || !$image->save(storage_path("app/public/{$typeUrl}/{$file->name}"), 80, 'jpg')) {
            return response()->json([
                'message' => __("Erro ao tentar fazer upload."),
            ], 400);
        }
        return response()->json($file);
    }

    public function delete(FileDeleteRequest $request, $id)
    {
        $user = $request->user();
        $data = File::whereCompanyId($user->company_id)
            ->find($id);
        if(!$data->delete()) {
            return response()->json([
                'message' => __("Erro ao tentar remover."),
            ], 400);
        }
        return response()->json([
            'message' => __('Arquivo removido com sucesso.'),
        ], 200);
    }

    public function images(Request $request) {
        $request->validate([
            'type' => ['required'],
            'id' => ['required'],
        ]);
        $images = File::whereType($request->get('type'))
            ->whereTypeId($request->get('id'))
            ->get();
        return response()->json([
            'data' => $images,
        ]);
    }
}
