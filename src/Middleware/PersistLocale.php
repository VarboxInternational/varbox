<?php

namespace Varbox\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Varbox\Contracts\LanguageModelContract;

class PersistLocale
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var LanguageModelContract
     */
    protected $language;

    /**
     * @param Application $app
     * @param LanguageModelContract $language
     */
    public function __construct(Application $app, LanguageModelContract $language)
    {
        $this->app = $app;
        $this->language = $language;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('locale')) {
            $this->app->setLocale($request->session()->get('locale'));
        } elseif (config('varbox.translation.auto_detect_locale') === true) {
            $this->app->setLocale($request->getPreferredLanguage(
                $this->language->onlyActive()->pluck('code')->toArray()
            ));
        }

        return $next($request);
    }
}
