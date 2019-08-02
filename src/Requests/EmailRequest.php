<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Varbox\Contracts\EmailModelContract;

class EmailRequest extends FormRequest
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
        $model = $this->model();

        return [
            'name' => [
                'required',
                'min:3',
                Rule::unique('emails', 'name')
                    ->ignore($model && $model->exists ? $model->getKey() : null),
            ],
            'type' => [
                'required',
                Rule::in(array_keys((array)config('varbox.emails.types', []))),
                Rule::unique('emails', 'type')
                    ->ignore($model && $model->exists ? $model->getKey() : null),
            ],
        ];
    }

    /**
     * Get the model by extracting it from one of the following:\
     * - implicit/explicit route model binding
     * - model id as route parameter
     *
     * @return \Illuminate\Routing\Route|object|string|null
     */
    protected function model()
    {
        if ($this->route('email')) {
            return $this->route('email');
        }

        if ($this->route('id')) {
            return app(EmailModelContract::class)->withDrafts()->withTrashed()
                ->find($this->route('id'));
        }

        return null;
    }
}
