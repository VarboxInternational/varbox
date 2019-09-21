<?php

namespace Varbox\Composers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\View\View;
use Varbox\Contracts\LanguageModelContract;

class LanguagesComposer
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
     * Construct the admin menu.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        if (defined('IS_TRANSLATABLE') && IS_TRANSLATABLE === true) {
            $languages = $this->language->onlyActive()->get();

            if (!($language = $languages->where('code', $this->app->getLocale())->first())) {
                $language = $languages->first();
            }

            $view->with([
                'language' => $language,
                'languages' => $languages,
                'show' => true,
            ]);
        }
    }
}
