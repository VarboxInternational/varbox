<?php

namespace Varbox\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Varbox\Contracts\LanguageModelContract;

class IsTranslatable
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
        $default = $this->language->onlyDefault()->first();

        if ($this->app->getLocale() != $default->code && $this->isOnCreate()) {
            $this->app->setLocale($default->code);
            $request->session()->put('locale', $default->code);

            flash()->warning($this->warningMessage($default));
        }

        define('IS_TRANSLATABLE', true);

        return $next($request);
    }

    /**
     * Determine if the current request is for "creating" an entity record.
     *
     * @return bool
     */
    protected function isOnCreate()
    {
        return Str::endsWith(Route::getCurrentRoute()->getActionName(), '@create');
    }

    /**
     * Get the warning message to show the user when trying to create an entity in a non-default locale.
     *
     * @param LanguageModelContract $language
     * @return string
     */
    protected function warningMessage(LanguageModelContract $language)
    {
        return
            'You are trying to add an entity in other language than the default one!<br />' .
            'The language has been switched back to <strong>' . $language->name . '</strong>.';
    }
}
