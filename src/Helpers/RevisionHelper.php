<?php

namespace Varbox\Helpers;

use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\RevisionHelperContract;
use Varbox\Contracts\RevisionModelContract;

class RevisionHelper implements RevisionHelperContract
{
    /**
     * Build the revision container html.
     *
     * @param string $route
     * @param Model $model
     * @param RevisionModelContract|null $revision
     * @param array $parameters
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function container($route, Model $model, RevisionModelContract $revision = null, $parameters = [])
    {
        return view('varbox::helpers.revision.container')->with([
            'model' => $model,
            'revision' => $revision,
            'route' => $route,
            'parameters' => $parameters,
        ]);
    }

    /**
     * Build the additional revision view html.
     *
     * @param RevisionModelContract $revision
     * @param Model $model
     * @return \Illuminate\View\View
     */
    public function view(RevisionModelContract $revision, Model $model)
    {
        return view('varbox::helpers.revision.view')->with([
            'revision' => $revision,
            'model' => $model,
        ]);
    }
}
