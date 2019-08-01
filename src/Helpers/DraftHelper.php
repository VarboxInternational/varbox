<?php

namespace Varbox\Helpers;

use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\DraftHelperContract;

class DraftHelper implements DraftHelperContract
{
    /**
     * Build the draft container html.
     *
     * @param string $route
     * @param Model $model
     * @return \Illuminate\View\View
     */
    public function container($route, Model $model)
    {
        return view('varbox::helpers.draft.container')->with([
            'route' => $route,
            'model' => $model,
        ]);
    }
}
