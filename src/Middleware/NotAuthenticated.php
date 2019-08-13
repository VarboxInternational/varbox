<?php

namespace Varbox\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotAuthenticated
{
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
     * The request paths ignoring this middleware.
     *
     * @return array
     */
    protected function exceptions()
    {
        return [
            config('varbox.admin.prefix', 'admin') . '/logout',
        ];
    }

    /**
     * Establish if request path is an exception or not.
     *
     * @param Request $request
     * @return bool
     */
    protected function isException($request)
    {
        foreach ($this->exceptions() as $exception) {
            if ($exception !== '/') {
                $exception = trim($exception, '/');
            }

            if ($request->is($exception)) {
                return true;
            }
        }

        return false;
    }
}
