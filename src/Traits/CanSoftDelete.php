<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;

trait CanSoftDelete
{
    /**
     * Get the model to be soft deleted.
     *
     * @return Model
     */
    abstract protected function softDeleteModel(): string;

    /**
     * Get the url to redirect to after the soft deletion.
     *
     * @return string
     */
    abstract protected function softDeleteRedirectTo(): string;

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function restore(Request $request, $id)
    {
        $entity = app($this->softDeleteModel());

        if (!$this->canBeSoftDeleted($entity)) {
            flash()->error('This entity cannot be restored because it\'s not soft-deletable!');

            return back();
        }

        try {
            $model = $entity->newQueryWithoutScopes()
                ->withGlobalScope(SoftDeletingScope::class, new SoftDeletingScope)
                ->onlyTrashed()->findOrFail($id);

            /*if (in_array(HasBlocks::class, class_uses($entity))) {
                $model->doNotSaveBlocks();
            }*/

            $model->restore();

            flash()->success($this->restoreSuccessMessage());

            return redirect($this->softDeleteRedirectTo());
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function delete(Request $request, $id)
    {
        $entity = app($this->softDeleteModel());

        if (!$this->canBeSoftDeleted($entity)) {
            flash()->error('This entity cannot be force-deleted because it\'s not soft-deletable!');

            return back();
        }

        try {
            $entity->newQueryWithoutScopes()
                ->withGlobalScope(SoftDeletingScope::class, new SoftDeletingScope)
                ->onlyTrashed()->findOrFail($id)->forceDelete();

            flash()->success($this->forceDeleteSuccessMessage());

            return redirect($this->softDeleteRedirectTo());
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if a model can be duplicated.
     * It has to use the Varbox\Traits\HasDuplicates trait.
     *
     * @param Model $model
     * @return bool
     */
    protected function canBeSoftDeleted(Model $model)
    {
        return in_array(SoftDeletes::class, class_uses($model));
    }

    /**
     * Get the message to display after a "restore" process has been executed successfully.
     *
     * @return string
     */
    protected function restoreSuccessMessage()
    {
        return 'The record was successfully restored!';
    }

    /**
     * Get the message to display after a "force delete" process has been executed successfully.
     *
     * @return string
     */
    protected function forceDeleteSuccessMessage()
    {
        return 'The record was successfully force deleted!';
    }
}
