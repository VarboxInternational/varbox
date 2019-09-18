<?php

namespace Varbox\Middleware;

use Closure;
use Varbox\Contracts\RedirectModelContract;

class RedirectRequests
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $redirect = app(RedirectModelContract::class)->findValidOrNull($request->path());

        if (! $redirect && $request->getQueryString()) {
            $path = $request->path().'?'.$request->getQueryString();
            $redirect = app(RedirectModelContract::class)->findValidOrNull($path);
        }

        if ($redirect && $redirect->exists) {
            return redirect($redirect->new_url, $redirect->status);
        }

        return $next($request);
    }
}
