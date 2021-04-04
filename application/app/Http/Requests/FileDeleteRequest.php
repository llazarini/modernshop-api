<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileDeleteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => [sprintf('exists:files,id,company_id,%s', $this->user()->company_id)]
        ];
    }
}
