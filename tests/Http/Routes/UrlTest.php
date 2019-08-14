<?php

namespace Varbox\Tests;

use Illuminate\Support\Facades\Route;
use Varbox\Tests\Http\TestCase;
use Varbox\Tests\Models\Post;

class UrlTest extends TestCase
{
    /**
     * @var Post
     */
    protected $post;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Route::url();

        $this->post = Post::create([
            'name' => 'Test name',
        ]);
    }

    /** @test */
    public function it_dispatches_the_url_to_the_specified_endpoint()
    {
        $this->get($this->post->url->url)->assertStatus(200);
    }

    /** @test */
    public function it_supplies_the_urlable_model_when_dispatching()
    {
        $this->get($this->post->url->url)->assertSee('Test name');
    }
}
