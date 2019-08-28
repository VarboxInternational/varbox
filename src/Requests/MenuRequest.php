<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
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
            ],
            'type' => [
                'required',
                Rule::in(array_keys((array)config('varbox.menus.types', []))),
            ],
        ];
    }

    /**
     * Merge the request with the extra necessary info.
     *
     * @param string|null $location
     * @return $this
     */
    public function merged($location = null)
    {
        if ($location) {
            $this->mergeLocation($location);
        }

        return $this->mergeActive()->mergeWindow();
    }

    /**
     * Merge the "location" field.
     *
     * @param string $location
     * @return $this
     */
    protected function mergeLocation($location)
    {
        $this->merge([
            'location' => $location
        ]);

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

    /**
     * Instantiate the "new window" field to false if not supplied.
     *
     * @return $this
     */
    protected function mergeWindow()
    {
        if (!$this->filled('data.new_window')) {
            $this->merge([
                'data' => [
                    'new_window' => false,
                ]
            ]);
        }

        return $this;
    }
}
