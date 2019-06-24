<?php

use Illuminate\Support\Facades\Route;

Route::get('{all}', ['uses' => '\Varbox\Controllers\Controller@show'])->where('all', '(.*)');