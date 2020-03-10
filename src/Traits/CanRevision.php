<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Varbox\Contracts\QueryCacheServiceContract;
use Varbox\Contracts\RevisionModelContract;

trait CanRevision
{
    /**
     * Get the title to be used on the revision view.
     *
     * The title will be used in:
     * - page title
     * - meta title
     *
     * @return string
     */
    abstract protected function revisionPageTitle(): string;

    /**
     * Get the blade view to be rendered as the revision view.
     *
     * @return string
     */
    abstract protected function revisionView(): string;

    /**
     * Get additional view variables to be assigned to the revision view.
     * If no additional variables are needed, return an empty array.
     *
     * @param Model $revisionable
     * @return array
     */
    abstract protected function revisionViewVariables(Model $revisionable): array;

    /**
     * Display the revision view.
     * Set a back url in the session so we know where to redirect.
     * Set the revision page meta title.
     * Display the revision view.
     *
     * @param QueryCacheServiceContract $cache
     * @param RevisionModelContract $revision
     * @return \Illuminate\View\View
     * @throws Exception
     */
    public function showRevision(QueryCacheServiceContract $cache, RevisionModelContract $revision)
    {
        if (!($revision instanceof RevisionModelContract && $revision->exists)) {
            $revision = Route::current()->parameter('revision');
        }

        $this->rememberRevisionBackUrl($revision);
        $this->establishRevisionPageTitle();

        try {
            DB::beginTransaction();

            $cache->disableQueryCache();

            $model = $revision->revisionable;

            if (!$this->canBeRevisioned($model)) {
                flash()->error('This entity cannot be revisioned!');

                return back();
            }

            $model->rollbackToRevision($revision);

            return $this->revisionViewWithVariables($model, $revision);
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();

            flash()->error('Could not display the revision! Please try again.', $e);

            return back();
        }
    }

    /**
     * Verify if a model can be revisioned.
     * It has to use the Varbox\Traits\HasRevisions trait.
     *
     * @param Model $model
     * @return bool
     */
    protected function canBeRevisioned(Model $model)
    {
        return $model && $model->exists && in_array(HasRevisions::class, class_uses($model));
    }

    /**
     * Remember the back url for when canceling, rolling back a revision.
     *
     * @param RevisionModelContract $revision
     * @return void
     */
    protected function rememberRevisionBackUrl(RevisionModelContract $revision)
    {
        if (!session('revision_back_url_' . $revision->getKey())) {
            session()->put('revision_back_url_' . $revision->getKey(), url()->previous());
        }
    }

    /**
     * Set the meta title for the revision view page.
     *
     * @return void
     * @throws Exception
     */
    protected function establishRevisionPageTitle()
    {
        $title = $this->revisionPageTitle();

        view()->share('title', $title ?: 'Revision');
        meta()->set('title', $title ? 'Admin - ' . $title : 'Admin');
    }

    /**
     * Build the revision view with every required or specified variable.
     *
     * @param Model $model
     * @param RevisionModelContract $revision
     * @return \Illuminate\View\View
     */
    protected function revisionViewWithVariables(Model $model, RevisionModelContract $revision)
    {
        return view($this->revisionView())->with(array_merge(
            $this->revisionViewVariables($model),
            ['item' => $model, 'revision' => $revision]
        ));
    }
}
