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

    /**
     * Merge the request with the extra necessary info.
     *
     * @return $this
     */
    public function merged()
    {
        return $this->mergeDefault()->mergeActive();
    }

    /**
     * Instantiate the "active" field to false if not supplied.
     *
     * @return $this
     */
    protected function mergeDefault()
    {
        if (!$this->filled('default')) {
            $this->merge([
                'default' => false
            ]);
        }

        return $this;
    }

    /**
     * Instantiate the "active" field to false if not supplied.
     *
     * @return $this
     */
    protected function mergeActive()
    {
        if (!$this->filled('active')) {
            $this->merge([
                'active' => false
            ]);
        }

        return $this;
    }
}
