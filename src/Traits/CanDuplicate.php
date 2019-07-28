<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Varbox\Exceptions\DuplicateException;

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
        $entity = app($class);

        if (!$this->canBeDuplicated($entity)) {
            flash()->error(
                $this->entityCannotBeDuplicatedMessage(),
                DuplicateException::cannotBeDuplicated($entity)
            );

            return back();
        }

        try {
            if (!($model instanceof $class)) {
                try {
                    $model = $entity->findOrFail($model);
                } catch (ModelNotFoundException $e) {
                    flash()->error($this->duplicateNonExistentMessage(), $e);

                    return back();
                }
            }

            $duplicate = $model->saveAsDuplicate();

            flash()->success($this->duplicateSuccessMessage());

            //return redirect()->route($this->routeToduplicateRedirectTo(), $duplicate->id);
            return redirect($this->duplicateRedirectTo($duplicate));
        } catch (Exception $e) {
            flash()->error($this->duplicateFailedMessage(), $e);

            return back()->withInput($request ? $request->all() : []);
        }
    }

    /**
     * Verify if a model can be duplicated.
     * It has to use the Varbox\Traits\HasDuplicates trait.
     *
     * @param Model $model
     * @return bool
     */
    protected function canBeDuplicated(Model $model)
    {
        return in_array(HasDuplicates::class, class_uses($model));
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

    /**
     * Get the message to display after a "duplicate" process has failed.
     *
     * @return string
     */
    protected function duplicateFailedMessage()
    {
        return 'Failed duplicating the record!';
    }

    /**
     * Get the message to display when trying to duplicate an un-duplicatable entity record.
     *
     * @return string
     */
    protected function entityCannotBeDuplicatedMessage()
    {
        return 'This entity record cannot be duplicated!';
    }

    /**
     * Get the message to display when trying to duplicate a non-existent database record.
     *
     * @return string
     */
    protected function duplicateNonExistentMessage()
    {
        return 'You are trying to duplicate a record that does not exist!';
    }
}
