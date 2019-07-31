<?php

namespace Varbox\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class DraftsController extends Controller
{
    /**
     * Save a draft.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function save(Request $request)
    {
        $data = $request->get('draft');

        $this->guardAgainstInvalidRequest($data);

        $id = $data['model_id'] ?? null;
        $class = $data['model_class'];
        $req = $data['validation_request'];
        $route = $data['redirect_route'];

        try {
            $this->validateModelData($req, $request->all());
        } catch (ValidationException $e) {
            return back()
                ->withInput($request->all())
                ->withErrors($e->validator->errors());
        }

        try {
            $model = $this->getDraftedModelById(app($class), $id);
        } catch (ModelNotFoundException $e) {
            $model = app($class);
        }

        $draft = $model->saveAsDraft($request->all());

        flash()->success('The draft was successfully ' . ($id ? 'updated' : 'created') . '!');

        return redirect()->route($route, $draft->id);
    }

    /**
     * Publish a drafted record.
     * Update the "drafted_at" column to null.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function publish(Request $request)
    {
        try {
            $this->getDraftedModelById(
                app($request->get('_class')), $request->get('_id')
            )->publishDraft();

            flash()->success('The record was published successfully!');

            return back();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the drafted model by id.
     * Account for trashed records also.
     *
     * @param Model $model
     * @param int $id
     * @return mixed
     */
    protected function getDraftedModelById(Model $model, $id)
    {
        $query = $model->withDrafts();

        if (in_array(SoftDeletes::class, class_uses($model))) {
            $query->withTrashed();
        }

        return $query->whereId((int)$id)->firstOrFail();
    }

    /**
     * Validate the entity's request data based on the request rules provided.
     *
     * @param Request $request
     * @param array $data
     * @throws ValidationException
     */
    protected function validateModelData($request = null, array $data = [])
    {
        if (!$request) {
            return;
        }

        $validation = (new $request)->rules();

        foreach ($validation as $field => $rules) {
            if (is_array($rules)) {
                foreach ($rules as $index => $rule) {
                    if (@get_class($rule) == Unique::class || Str::is('unique*', $rule)) {
                        unset($validation[$field][$index]);
                    }
                }
            } else {
                if (@get_class($rules) == Unique::class || Str::is('unique*', $rules)) {
                    unset($validation[$field]);
                }
            }
        }

        $validator = Validator::make($data, $validation);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate the crucial request data needed for creating a draft.
     * _class | _request | _id (optional)
     *
     * @param array $data
     * @return void
     * @throws Exception
     */
    protected function guardAgainstInvalidRequest(array $data = [])
    {
        $validator = Validator::make($data, [
            'model_class' => 'required',
            'validation_request' => 'required',
            'redirect_route' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException(
                'To be able to save a draft, please add the following hidden fields to the entity form' . PHP_EOL .
                '"model_class" => The fully qualified class name of the entity model' . PHP_EOL .
                '"validation_request" => The fully qualified class name of the request validating the entity model' . PHP_EOL .
                '"redirect_route" => Only route name to redirect to after saving the draft' . PHP_EOL .
                '"model_id" (optional) => The id of the entity model (if model exists)'
            );
        }
    }
}
