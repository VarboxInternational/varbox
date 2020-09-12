<?php

namespace Varbox\Tests\Controllers;

use Illuminate\Routing\Controller;
use Varbox\Models\Url;

class UrlController extends Controller
{
    public function show()
    {
        $model = Url::findUrlableOrFail();

        return $model->name;
    }
}
