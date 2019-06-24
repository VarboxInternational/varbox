<?php

namespace Varbox\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @param Authenticatable $user
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Authenticatable $user)
    {
        return view('varbox::admin.dashboard');
    }
}
