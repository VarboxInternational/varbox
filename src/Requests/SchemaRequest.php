<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Varbox\Contracts\SchemaModelContract;

class SchemaRequest extends FormRequest
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
                Rule::unique('schema', 'name')
                    ->ignore($this->route('schema') ? $this->route('schema')->id : null)
            ],
            'type' => [
                'required',
                Rule::in(array_keys(app(SchemaModelContract::class)->getTypes()))
            ],
            'target' => [
                'required',
                Rule::in(array_keys((array)config('varbox.schema.targets', [])))
            ]
        ];
    }
}
