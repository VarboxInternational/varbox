<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                Rule::unique('languages', 'name')
                    ->ignore($this->route('language') ? $this->route('language')->getKey() : null)
            ],
            'code' => [
                'required',
                Rule::unique('languages', 'code')
                    ->ignore($this->route('language') ? $this->route('language')->getKey() : null)
            ],
            'default' => [
                'boolean',
            ],
        ];
    }
}
