<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StateRequest extends FormRequest
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
            'country_id' => [
                'required',
                'numeric',
                Rule::exists('countries', 'id'),
            ],
            'name' => [
                'required',
                Rule::unique('states', 'name')
                    ->ignore(optional($this->route('state'))->getKey()),
            ],
            'code' => [
                'required',
                Rule::unique('states', 'code')
                    ->ignore(optional($this->route('state'))->getKey()),
            ],
        ];
    }

    /**
     * Get the pretty name of attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'country_id' => 'country',
        ];
    }
}
