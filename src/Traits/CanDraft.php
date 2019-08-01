<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;

trait CanDraft
{
    /**
     * Get the model to be drafted.
     *
     * @return Model
     */
    abstract protected function draftModel(): string;

    /**
     * Get the form request to validate the draft upon.
     *
     * @return string|null
     */
    abstract protected function draftRequest(): ?string;

    /**
     * Get the url to redirect after drafting/publishing.
     *
     * @param Model $model
     * @return string
     */
    abstract protected function draftRedirectTo(Model $model): string;

    /**
     * @param Request $request
     * @param Model|null $model
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws Exception
     */
    public function saveDraft(Request $request, $model = null)
    {
        $class = $this->draftModel();

        app($this->draftRequest());

        try {
            if (!$model) {
                $model = app($class);
            }

            if (!($model instanceof $class)) {
                $model = $this->findDraftedModel($class, $model);
            }

            $draft = $model->saveAsDraft($request->all());

            flash()->success($this->draftSuccessMessage($model->exists));

            return redirect($this->draftRedirectTo($draft));
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Publish a drafted record.
     * Update the "drafted_at" column to null.
     *
     * @param Request $request
     * @param Model|int $model
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function publishDraft(Request $request, $model)
    {
        $class = $this->draftModel();

        try {
            if (!($model instanceof $class)) {
                $model = $this->findDraftedModel($class, $model);
            }

            $model = $model->publishDraft();

            flash()->success($this->publishSuccessMessage());

            return redirect($this->draftRedirectTo($model));
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the drafted model by id.
     * Account for trashed records also.
     *
     * @param string $class
     * @param int $id
     * @return mixed
     */
    protected function findDraftedModel($class, $id)
    {
        $query = $class::withDrafts();

        if (in_array(SoftDeletes::class, class_uses($class))) {
            $query->withTrashed();
        }

        return $query->whereId((int)$id)->firstOrFail();
    }

    /**
     * Validate the entity's request data based on the request rules provided.
     *
     * @param array $data
     * @throws ValidationException
     */
    protected function validateDraftData(array $data = [])
    {
        $request = $this->draftRequest();

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
     * Get the message to display after a "publish" process has been executed successfully.
     *
     * @param bool $exists
     * @return string
     */
    protected function draftSuccessMessage($exists = false)
    {
        return 'The draft was successfully ' . ($exists == true ? 'updated' : 'created') . '!';
    }

    /**
     * Get the message to display after a "publish" process has been executed successfully.
     *
     * @return string
     */
    protected function publishSuccessMessage()
    {
        return 'The draft was successfully published!';
    }
}
