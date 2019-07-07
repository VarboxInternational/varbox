<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\Config;

class ConfigsTest extends TestCase
{
    /**
     * @var Config
     */
    protected $configModel;

    /**
     * @var string
     */
    protected $configKey = 'app.name';
    protected $configValue = 'Test App Name';

    /**
     * @var string
     */
    protected $configValueModified = 'Test App Name Modified';

    /**
     * @return void
     */
    protected function createConfig()
    {
        $this->configModel = Config::create([
            'key' => $this->configKey,
            'code' => $this->configValue,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteConfig()
    {
        Config::whereValue($this->configValue)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteCountryModified()
    {
        Config::whereValue($this->configValueModified)->first()->delete();
    }
}
