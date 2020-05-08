<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
                'min:3',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user') ? $this->route('user')->id : null)
            ],
            'password' => [
                'confirmed',
                $this->isMethod('post') ? 'required' : null
            ],
            'roles' => [
                'nullable',
                'array',
                Rule::exists('roles', 'id')->where('guard', 'web')
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
        return $this->mergePassword()->mergeActive();
    }

    /**
     * Secure the "password" field if supplied.
     * Modify the request to not validate the "password" field if not supplied.
     *
     * @return $this
     */
    protected function mergePassword()
    {
        if ($this->filled('password')) {
            return $this->merge([
                'password' => bcrypt($this->input('password')),
            ]);
        } else {
            return $this->create($this->url(), $this->method(), $this->except([
                'password', 'password_confirmation'
            ]));
        }
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
