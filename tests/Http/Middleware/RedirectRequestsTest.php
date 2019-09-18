<?php

namespace Varbox\Tests\Http\Middleware;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Middleware\RedirectRequests;
use Varbox\Models\Redirect;
use Varbox\Tests\Http\TestCase;

class RedirectRequestsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->make(Kernel::class)->pushMiddleware(RedirectRequests::class);
    }

    /** @test */
    public function it_redirects_a_request()
    {
        Redirect::create([
            'old_url' => 'old-url',
            'new_url' => 'new/url',
        ]);

        $response = $this->get('old-url');
        $response->assertRedirect('new/url');
    }

    /** @test */
    public function it_redirects_nested_requests()
    {
        Redirect::create([
            'old_url' => '1',
            'new_url' => '2',
        ]);

        $response = $this->get('1');
        $response->assertRedirect('2');

        Redirect::create([
            'old_url' => '2',
            'new_url' => '3',
        ]);

        $response = $this->get('1');
        $response->assertRedirect('3');

        $response = $this->get('2');
        $response->assertRedirect('3');

        Redirect::create([
            'old_url' => '3',
            'new_url' => '4',
        ]);

        $response = $this->get('1');
        $response->assertRedirect('4');

        $response = $this->get('2');
        $response->assertRedirect('4');

        $response = $this->get('3');
        $response->assertRedirect('4');

        Redirect::create([
            'old_url' => '4',
            'new_url' => '1',
        ]);

        $response = $this->get('2');
        $response->assertRedirect('1');

        $response = $this->get('3');
        $response->assertRedirect('1');

        $response = $this->get('4');
        $response->assertRedirect('1');
    }
}
