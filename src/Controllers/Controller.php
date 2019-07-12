<?php

namespace Varbox\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;
use Varbox\Models\Url;

class Controller extends BaseController
{
    /**
     * Dispatch the request to the designated controller and action.
     *
     * @param string $url
     * @return mixed
     */
    public function show($url = '/')
    {
        try {
            $url = Url::whereUrl($url)->firstOrFail();

            if ($model = $url->urlable) {
                return (new ControllerDispatcher(app()))->dispatch(app(Route::class)->setAction([
                    'model' => $model
                ]), app($model->getUrlOptions()->routeController), $model->getUrlOptions()->routeAction);
            } else {
                abort(404);
            }
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }
}