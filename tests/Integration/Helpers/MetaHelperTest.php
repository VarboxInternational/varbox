<?php

namespace Varbox\Tests\Integration\Helpers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Helpers\MetaHelper;
use Varbox\Tests\Integration\TestCase;

class MetaHelperTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var MetaHelper
     */
    protected $meta;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->meta = new MetaHelper;

        $this->app['config']->set('varbox.meta.default_values', [
            'title' => 'default title'
        ]);
    }

    /** @test */
    public function it_can_set_and_get_meta_tags()
    {
        $this->meta->set('title', 'test title');

        $this->assertEquals('test title', $this->meta->get('title'));
    }

    /** @test */
    public function it_returns_the_default_value_if_no_tag_value_is_specified()
    {
        $this->assertEquals('default title', $this->meta->get('title'));
    }

    /** @test */
    public function it_transforms_meta_tags()
    {
        $this->meta->set('title', 'test title');
        $this->meta->set('description', 'test description');

        $this->assertEquals('<title>test title</title>', $this->meta->tag('title'));
        $this->assertEquals('<meta name="description" content="test description" />', $this->meta->tag('description'));
    }

    /** @test */
    public function it_transforms_open_graph_tags()
    {
        $this->meta->set('og:title', 'test title');

        $this->assertEquals('<meta property="og:title" content="test title" />', $this->meta->tag('og:title'));
    }

    /** @test */
    public function it_transforms_twitter_tags()
    {
        $this->meta->set('twitter:title', 'test title');

        $this->assertEquals('<meta name="twitter:title" content="test title" />', $this->meta->tag('twitter:title'));
    }
}
