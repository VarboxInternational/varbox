<?php

namespace Varbox\Tests\Http\Middleware;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Varbox\Exceptions\AuthenticationException;
use Varbox\Models\User;
use Varbox\Tests\Http\TestCase;

class AuthenticatedTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Route::middleware('varbox.authenticated')->get('/_test/authenticated', function () {
            return 'OK';
        });
    }

    /** @test */
    public function it_doesnt_allow_unauthenticated_users()
    {
        $this->withoutExceptionHandling();

        try {
            $this->get('/_test/authenticated');
        } catch (AuthenticationException $e) {
            $this->assertEquals('Unauthenticated.', $e->getMessage());

            return;
        }

        $this->fail('Expected Varbox\Exceptions\AuthenticationException');
    }

    /** @test */
    public function it_allows_authenticated_users()
    {
        $this->withoutExceptionHandling();
        $this->createUser();

        $response = $this->actingAs($this->user)->get('/_test/authenticated');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
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
