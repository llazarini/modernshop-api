<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => ['required'],
            'type_id' => [''],
            'name' => ['required']
        ];
    }
}
