<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Varbox\Contracts\PageModelContract;

class PageRequest extends FormRequest
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
                Rule::unique('pages', 'name')
                    ->ignore($model && $model->exists ? $model->getKey() : null),
            ],
            'slug' => [
                'required',
                Rule::unique('pages', 'slug')
                    ->ignore($model && $model->exists ? $model->id : null)
            ],
            'type' => [
                'required',
                Rule::in(array_keys((array)config('varbox.pages.types', []))),
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
        if ($this->route('page')) {
            return $this->route('page');
        }

        if ($this->route('id')) {
            return app(PageModelContract::class)->withDrafts()->withTrashed()
                ->find($this->route('id'));
        }

        return null;
    }
}
