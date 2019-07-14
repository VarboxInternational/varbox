<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Varbox\Models\Upload;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (config('varbox.upload.database.save', true) !== true) {
            return [];
        }

        return [
            'name' => [
                'required',
            ],
            'original_name' => [
                'required',
            ],
            'path' => [
                'required',
            ],
            'full_path' => [
                'required',
                Rule::unique('uploads', 'name')
                    ->ignore($this->route('upload') ? $this->route('upload')->id : null)
            ],
            'extension' => [
                'required',
            ],
            'size' => [
                'required',
                'numeric',
            ],
            'mime' => [
                'required',
            ],
            'type' => [
                'required',
                'numeric',
                Rule::in(array_keys(Upload::getFileTypes())),
            ],
        ];
    }
}
