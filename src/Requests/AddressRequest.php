<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressRequest extends FormRequest
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
        $stateExistsRule = Rule::exists('states', 'id');
        $cityExistsRule = Rule::exists('cities', 'id');

        if ($this->country_id) {
            $stateExistsRule->where('country_id', $this->country_id);
            $cityExistsRule->where('country_id', $this->country_id);
        }

        if ($this->state_id) {
            $cityExistsRule->where('state_id', $this->state_id);
        }

        return [
            'user_id' => [
                'required',
                'numeric',
                Rule::exists('users', 'id'),
            ],
            'country_id' => [
                'nullable',
                'numeric',
                Rule::exists('countries', 'id'),
            ],
            'state_id' => [
                'nullable',
                'numeric',
                $stateExistsRule,
            ],
            'city_id' => [
                'nullable',
                'numeric',
                $cityExistsRule,
            ],
            'address' => [
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
            'user_id' => 'user',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
        ];
    }
}
