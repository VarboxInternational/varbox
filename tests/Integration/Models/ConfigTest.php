<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Config;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class ConfigTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Config
     */
    protected $config;

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Config::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Config::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Config::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Config::class));
    }

    /** @test */
    public function it_can_return_the_value()
    {
        $this->config = Config::create([
            'key' => 'app.name',
            'value' => 'Test App Name',
        ]);

        $this->assertEquals('Test App Name', $this->config->value);

        $this->config->update([
            'value' => 'First Test App Name;Second Test App Name'
        ]);

        $this->assertIsArray($this->config->value);
        $this->assertEquals('First Test App Name', $this->config->value[0]);
        $this->assertEquals('Second Test App Name', $this->config->value[1]);
    }

    /** @test */
    public function it_can_return_all_the_allowed_keys()
    {
        $this->app['config']->set('varbox.config.keys', [
            'app.name', 'auth.guards.default'
        ]);

        $keys = Config::getAllowedKeys();

        $this->assertIsArray($keys);
        $this->assertCount(2, $keys);
        $this->assertArrayHasKey('app.name', $keys);
        $this->assertArrayHasKey('auth.guards.default', $keys);
    }
}
