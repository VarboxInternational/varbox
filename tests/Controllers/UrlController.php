<?php

namespace Varbox\Tests\Controllers;

use Varbox\Models\Url;
use Illuminate\Routing\Controller;

class UrlController extends Controller
{
    public function show()
    {
        $model = Url::findUrlableOrFail();

        return $model->name;
    }
}
