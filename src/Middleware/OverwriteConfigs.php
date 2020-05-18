<?php

namespace Varbox\Middleware;

use Closure;
use Illuminate\Http\Request;
use Varbox\Models\Config;

class OverwriteConfigs
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
        foreach (Config::all() as $config) {
            if (in_array($config->key, config('varbox.config.keys', []))) {
                config()->set($config->key, $this->parseValue($config->value));
            }
        }

        return $next($request);
    }

    /**
     * @param string $value
     * @return array|bool|null
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            switch (strtolower($value)) {
                case 'null':
                    $value = null;
                    break;
                case 'true':
                    $value = true;
                    break;
                case 'false':
                    $value = false;
                    break;
                case '[]':
                    $value = [];
                    break;
                case 'array()':
                    $value = [];
                    break;
            }
        }

        return $value;
    }
}
