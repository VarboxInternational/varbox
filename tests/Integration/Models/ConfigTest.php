<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Config;
use Varbox\Tests\Integration\TestCase;

class ConfigTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->afterApplicationCreated(function () {
            $this->keys = Config::getAllowedKeys();
        });
    }

    /**
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.varbox-config.keys', ['app.name']);
    }

    

    /**
     * @return void
     */
    protected function createConfig()
    {
        $this->config = Country::create([
            'name' => 'Test Country Name',
            'code' => 'TCN',
        ]);
    }
}
