<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Varbox\Models\Role;
use Varbox\Models\User;
use Varbox\Tests\Integration\TestCase;

class CheckRolesTest extends TestCase
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

        $this->createUser();
        $this->createRoles();
    }

    /** @test */
    public function it_doesnt_allow_user_without_one_role()
    {
        Route::middleware('varbox.check.roles:role1')
            ->get('/_test/check-roles', function () {
                return 'OK';
            });

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($this->user)->get('/_test/check-roles');
        } catch (HttpException $e) {
            $this->assertEquals(401, $e->getStatusCode());

            return;
        }

        $this->fail('Expected Symfony\Component\HttpKernel\Exception\HttpException -> 401');
    }

    /** @test */
    public function it_doesnt_allow_user_without_multiple_roles()
    {
        Route::middleware('varbox.check.roles:role1,role2')
            ->get('/_test/check-roles', function () {
                return 'OK';
            });

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($this->user)->get('/_test/check-roles');
        } catch (HttpException $e) {
            $this->assertEquals(401, $e->getStatusCode());

            return;
        }

        $this->fail('Expected Symfony\Component\HttpKernel\Exception\HttpException -> 401');
    }

    /** @test */
    public function it_doesnt_allow_user_without_all_roles()
    {
        $this->user->assignRoles('role1');

        Route::middleware('varbox.check.roles:role1,role2')
            ->get('/_test/check-roles', function () {
                return 'OK';
            });

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($this->user)->get('/_test/check-roles');
        } catch (HttpException $e) {
            $this->assertEquals(401, $e->getStatusCode());

            return;
        }

        $this->fail('Expected Symfony\Component\HttpKernel\Exception\HttpException -> 401');
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

    /**
     * @return void
     */
    protected function createRoles()
    {
        for ($i = 1; $i <= 3; $i++) {
            Role::create([
                'name' => 'role' . $i,
                'guard' => config('auth.defaults.guard'),
            ]);
        }
    }
}
