<?php

namespace Varbox\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\ImageOptimizer\OptimizerChain;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OptimizeImages
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $optimizerChain = app(OptimizerChain::class);

        collect($request->allFiles())->flatten()->each(function (UploadedFile $file) use ($optimizerChain) {
            if ($file->getError() == 0 && $file->getSize() > 0) {
                $optimizerChain->optimize($file->getPathname());
            }
        });

        return $next($request);
    }
}
