<?php

namespace Varbox\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotAuthenticated
{
    /**
     * The request paths ignoring this middleware.
     *
     * @var array
     */
    protected $except = [
        'logout',
        'admin/logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->isException($request)) {
            return $next($request);
        }

        if (auth()->guard($guard)->check()) {
            return $guard == 'admin' ? redirect()->route('admin') : redirect('/');
        }

        return $next($request);
    }

    /**
     * Establish if request path is an exception or not.
     *
     * @param Request $request
     * @return bool
     */
    protected function isException($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
