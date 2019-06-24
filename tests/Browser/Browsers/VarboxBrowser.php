<?php

namespace Varbox\Tests\Browser\Browsers;

use Laravel\Dusk\Browser as DuskBrowser;
use Laravel\Dusk\ElementResolver;
use Varbox\Tests\Browser\Concerns\InteractsWithCrud;

class VarboxBrowser extends DuskBrowser
{
    use InteractsWithCrud;

    /**
     * Create a browser instance.
     *
     * @param  \Facebook\WebDriver\Remote\RemoteWebDriver  $driver
     * @param  ElementResolver  $resolver
     * @return void
     */
    public function __construct($driver, $resolver = null)
    {
        parent::__construct($driver, $resolver);
    }
}