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
    public function container(Model $model, $route, RevisionModelContract $revision = null, $parameters = [])
    {
        return view('varbox::helpers.revision.container')->with([
            'model' => $model,
            'route' => $route,
            'revision' => $revision,
            'parameters' => $parameters,
        ]);
    }
}
