<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RedirectRequest extends FormRequest
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
            'old_url' => [
                'required',
                Rule::unique('redirects', 'old_url')
                    ->ignore($this->route('redirect') ? $this->route('redirect')->id : null)
            ],
            'new_url' => [
                'required',
            ],
            'status' => [
                'required',
                Rule::in(array_keys((array)config('varbox.redirect.statuses', [])))
            ],
        ];
    }
}
