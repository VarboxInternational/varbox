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
     * @param Model $model
     * @param string $route
     * @param array $parameters
     * @return \Illuminate\View\View
     */
    public function container(Model $model, $routeName, $routeParameters = [])
    {
        return view('varbox::helpers.revision.container')->with([
            'model' => $model,
            'routeName' => $routeName,
            'routeParameters' => $routeParameters,
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
