<?php

namespace Varbox\Helpers;

use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\DraftHelperContract;

class DraftHelper implements DraftHelperContract
{
    /**
     * Build the draft container html.
     *
     * @param Model $model
     * @param string $route
     * @param string|null $permission
     * @return \Illuminate\View\View
     */
    public function container(Model $model, $route, $permission = null)
    {
        $showPublishButton = true;

        if ($permission) {
            $showPublishButton = auth()->user()->isSuper() || auth()->user()->hasPermission($permission);
        }

        return view('varbox::helpers.draft.container')->with([
            'model' => $model,
            'route' => $route,
            'showPublishButton' => $showPublishButton,
        ]);
    }
}
