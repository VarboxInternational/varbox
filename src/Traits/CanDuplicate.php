<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

trait CanDuplicate
{
    /**
     * Get the model to be duplicated.
     *
     * @return Model
     */
    abstract protected function duplicateModel(): string;

    /**
     * Get the url to redirect after duplication
     *
     * @param Model $duplicate
     * @return string
     */
    abstract protected function duplicateRedirectTo(Model $duplicate): string;

    /**
     * Duplicate the given entity record.
     *
     * @param Request $request
     * @param Model|int $model
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function duplicate(Request $request, $model)
    {
        $class = $this->duplicateModel();

        try {
            if (!($model instanceof $class)) {
                $model = $class::findOrFail($model);
            }

            $duplicate = $model->saveAsDuplicate();

            flash()->success($this->duplicateSuccessMessage());

            return redirect($this->duplicateRedirectTo($duplicate));
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the message to display after a "duplicate" process has been executed successfully.
     *
     * @return string
     */
    protected function duplicateSuccessMessage()
    {
        return 'The record was successfully duplicated!<br />You have been redirected to the newly duplicated record.';
    }
}
