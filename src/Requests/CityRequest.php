<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
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
            'state_id' => [
                'nullable',
                'numeric',
                Rule::exists('states', 'id')
                    ->where('country_id', $this->country_id),
            ],
            'name' => [
                'required',
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
            'state_id' => 'state',
        ];
    }
}
