<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
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
                Rule::unique('countries', 'name')
                    ->ignore(optional($this->route('country'))->getKey())
            ],
            'code' => [
                'required',
                Rule::unique('countries', 'code')
                    ->ignore(optional($this->route('country'))->getKey())
            ],
        ];
    }
}
