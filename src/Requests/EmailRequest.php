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
        $model = null;

        if ($this->route('email')) {
            $model = $this->route('email');
        } elseif ($this->route('id')) {
            //$model = app('email.model')->withDrafts()->withTrashed()->find($this->route('id'));
            $model = app(EmailModelContract::class)->find($this->route('id'));
        }

        return [
            'name' => [
                'required',
                'min:3',
                Rule::unique('emails', 'name')
                    ->ignore($model && $model->exists ? $model->id : null),
            ],
            'type' => [
                'required',
                Rule::in(array_keys(app(EmailModelContract::class)->getTypes()))
            ],
        ];
    }
}
