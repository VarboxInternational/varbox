<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route as Router;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Varbox\Contracts\QueryCacheServiceContract;
use Varbox\Helpers\RelationHelper;

trait CanPreview
{
    /**
     * Get the model to be previewed.
     *
     * @return Model
     */
    abstract protected function previewModel(): string;

    /**
     * Get the controller where to dispatch the preview.
     *
     * @param Model $model
     * @return Model
     */
    abstract protected function previewController(Model $model): string;

    /**
     * Get the action where to dispatch the preview.
     *
     * @param Model $model
     * @return Model
     */
    abstract protected function previewAction(Model $model): string;

    /**
     * Get the form request to validate the preview upon.
     *
     * @return string|null
     */
    abstract protected function previewRequest(): ?string;

    /**
     * Preview an entity that has a url.
     *
     * @param Request $request
     * @param QueryCacheServiceContract $cache
     * @param int|null $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function preview(Request $request, QueryCacheServiceContract $cache, \Illuminate\Routing\Router $router, $id = null)
    {
        $req = app($this->previewRequest());

        if ($req instanceof ValidatesWhenResolved) {
            $validator = $this->makePreviewValidator($request, $req);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors());
            }
        }

        DB::beginTransaction();

        $cache->disableQueryCache();

        $model = $this->newOrExistingModelForPreview($id);
        $model = $this->saveModelForPreview($model, $request);
        $model = $this->savePivotedRelationForPreview($request, $model);

        $this->markAsPreview($model);

        return $this->executePreviewRequest($model);
    }

    /**
     * Mark the current request as a preview request, so the underlying logic would know that.
     *
     * @param Model $model
     * @return void
     */
    protected function markAsPreview(Model $model)
    {
        session()->flash('is_previewing', true);
    }

    /**
     * Set the model to a valid model record.
     * Based on the $id provided, the model will be new or loaded.
     *
     * @param Model|int|null $model
     * @return Model
     */
    protected function newOrExistingModelForPreview($model = null)
    {
        $class = $this->previewModel();

        if ($model instanceof $class) {
            return $model;
        }

        if ($model = app($class)->find($model)) {
            return $model;
        }

        abort(404);
    }

    /**
     * Dispatch the request to the front-end endpoint defined inside the model class that's being previewed.
     * The routing is done based on the "controller" and "action" defined on the HasUrl trait.
     *
     * @param Model $model
     * @return mixed
     */
    protected function executePreviewRequest(Model $model)
    {
        $dispatcher = (new ControllerDispatcher(app()))->dispatch(
            app(Router::class)->setAction(['model' => $model]),
            app($this->previewController($model)),
            $this->previewAction($model)
        );

        DB::rollBack();

        return $dispatcher;
    }

    /**
     * Save the given model and it's defined pivoted relations with the request provided.
     * Persist the model saves to the model property to be used later.
     *
     * @param Model $model
     * @param Request $request
     * @return Model
     */
    protected function saveModelForPreview(Model $model, Request $request)
    {
        if ($model && $model->exists) {
            $model->update($request->all());
        } else {
            $model = $model->create($request->all());
        }

        return $model;
    }

    /**
     * Save a defined pivoted relation of the model for the preview.
     *
     * @param Request $request
     * @param Model $model
     * @return Model
     * @throws \ReflectionException
     */
    protected function savePivotedRelationForPreview(Request $request, Model $model)
    {
        foreach (RelationHelper::getModelRelations($model) as $relation => $attributes) {
            if (!RelationHelper::isPivoted($attributes['type'])) {
                continue;
            }

            $data = $request->input($relation);

            $model->{$relation}()->detach();

            if (is_array($data)) {
                switch (array_depth($data)) {
                    case 1:
                        $model->{$relation}()->attach($data);

                        break;
                    case 2:
                        foreach ($data as $id => $attributes) {
                            $model->{$relation}()->attach($id, $attributes);
                        }

                        break;
                    case 3:
                        foreach ($data as $index => $parameters) {
                            foreach ($parameters as $id => $attributes) {
                                $model->{$relation}()->attach($id, $attributes);
                            }
                        }

                        break;
                }
            }
        }

        return $model;
    }

    /**
     * Parse the original form request validation rules into previewable rules.
     * Basically, strip any unique validation rule that might exist.
     *
     * @param Request $request
     * @param ValidatesWhenResolved $validator
     * @return mixed
     */
    protected function makePreviewValidator(Request $request, ValidatesWhenResolved $validator)
    {
        $validationRules = $validator->rules();

        foreach ($validationRules as $field => $rules) {
            if (is_array($rules)) {
                foreach ($rules as $index => $rule) {
                    if (@get_class($rule) == Unique::class || Str::is('unique*', $rule)) {
                        unset($validationRules[$field][$index]);
                    }
                }
            } else {
                if (@get_class($rules) == Unique::class || Str::is('unique*', $rules)) {
                    unset($validationRules[$field]);
                }
            }
        }

        return validator($request->all(), $validationRules, $validator->messages(), $validator->attributes());
    }
}
