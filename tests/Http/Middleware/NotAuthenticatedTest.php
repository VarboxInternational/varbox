<?php

namespace Varbox\Tests\Http\Middleware;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Varbox\Models\User;
use Varbox\Tests\Http\TestCase;

class NotAuthenticatedTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'users',
        ]);
    }

    /** @test */
    public function it_allows_unauthenticated_users()
    {
        $this->withoutExceptionHandling();

        Route::middleware('varbox.not.authenticated')->get('/_test/not-authenticated', function () {
            return 'OK';
        });

        $response = $this->get('/_test/not-authenticated');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    /** @test */
    public function it_doesnt_allow_admins()
    {
        $this->withoutExceptionHandling();
        $this->followingRedirects();
        $this->createUser();

        Route::get('admin', function () {
            return 'Admin';
        });

        Route::middleware('varbox.not.authenticated:admin')->get('/_test/not-authenticated', function () {
            return 'OK';
        });

        $response = $this->actingAs($this->user, 'admin')->get('/_test/not-authenticated');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Admin', $response->getContent());
    }

    /** @test */
    public function it_doesnt_allow_users()
    {
        $this->withoutExceptionHandling();
        $this->followingRedirects();
        $this->createUser();

        Route::get('/', function () {
            return 'Home';
        });

        Route::middleware('varbox.not.authenticated')->get('/_test/not-authenticated', function () {
            return 'OK';
        });

        $response = $this->actingAs($this->user)->get('/_test/not-authenticated');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Home', $response->getContent());
    }

    /**
     * @return void
     */
    protected function createUser()
    {
        $this->user = User::create([
            'email' => 'test-user@mail.com',
            'password' => bcrypt('test_password'),
        ]);
    }
}
