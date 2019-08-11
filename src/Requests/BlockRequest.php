<?php

namespace Varbox\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Varbox\Contracts\BlockModelContract;

class BlockRequest extends FormRequest
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
                Rule::unique('blocks', 'name')
                    ->ignore($model && $model->exists ? $model->getKey() : null)
            ],
            'type' => [
                'required',
                Rule::in(array_keys((array)config('varbox.blocks.types', []))),
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
        if ($this->route('block')) {
            return $this->route('block');
        }

        if ($this->route('id')) {
            return app(BlockModelContract::class)->withDrafts()->withTrashed()
                ->find($this->route('id'));
        }

        return null;
    }
}
