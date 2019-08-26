<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * @param Model|int $model
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function delete(Request $request, $model)
    {
        $class = $this->softDeleteModel();

        try {
            if (!($model instanceof $class)) {
                $model = (new $class)->newQueryWithoutScopes()
                    ->withGlobalScope(SoftDeletingScope::class, new SoftDeletingScope)
                    ->onlyTrashed()->findOrFail($model);
            }

            $model->forceDelete();

            flash()->success($this->forceDeleteSuccessMessage());

            return redirect($this->softDeleteRedirectTo());
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @param Model|int $model
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function restore(Request $request, $model)
    {
        $class = $this->softDeleteModel();

        try {
            if (!($model instanceof $class)) {
                $model = (new $class)->newQueryWithoutScopes()
                    ->withGlobalScope(SoftDeletingScope::class, new SoftDeletingScope)
                    ->onlyTrashed()->findOrFail($model);
            }

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
     * Get the message to display after a "force delete" process has been executed successfully.
     *
     * @return string
     */
    protected function forceDeleteSuccessMessage()
    {
        return 'The record was successfully force deleted!';
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
}
