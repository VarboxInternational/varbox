<?php

namespace Varbox\Tests\Browser\Browsers;

use Laravel\Dusk\Browser as DuskBrowser;
use Laravel\Dusk\ElementResolver;
use Varbox\Tests\Browser\Concerns\InteractsWithButtons;
use Varbox\Tests\Browser\Concerns\InteractsWithFilters;
use Varbox\Tests\Browser\Concerns\InteractsWithInputs;
use Varbox\Tests\Browser\Concerns\InteractsWithRecords;

class VarboxBrowser extends DuskBrowser
{
    use InteractsWithButtons;
    use InteractsWithInputs;
    use InteractsWithRecords;
    use InteractsWithFilters;

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
