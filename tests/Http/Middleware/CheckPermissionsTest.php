<?php

namespace Varbox\Tests\Http\Middleware;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Varbox\Models\Permission;
use Varbox\Models\Role;
use Varbox\Models\User;
use Varbox\Tests\Http\TestCase;

class CheckPermissionsTest extends TestCase
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
        $this->createPermissions();
    }

    /** @test */
    public function it_doesnt_allow_user_without_one_permission()
    {
        Route::middleware('varbox.check.permissions:permission1')
            ->get('/_test/check-permissions', function () {
                return 'OK';
            });

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($this->user)->get('/_test/check-permissions');
        } catch (HttpException $e) {
            $this->assertEquals(401, $e->getStatusCode());

            return;
        }

        $this->fail('Expected Symfony\Component\HttpKernel\Exception\HttpException -> 401');
    }

    /** @test */
    public function it_doesnt_allow_user_without_multiple_permissions()
    {
        Route::middleware('varbox.check.permissions:permission1,permission2')
            ->get('/_test/check-permissions', function () {
                return 'OK';
            });

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($this->user)->get('/_test/check-permissions');
        } catch (HttpException $e) {
            $this->assertEquals(401, $e->getStatusCode());

            return;
        }

        $this->fail('Expected Symfony\Component\HttpKernel\Exception\HttpException -> 401');
    }

    /** @test */
    public function it_doesnt_allow_user_without_all_permissions()
    {
        $this->user->grantPermission('permission1');

        Route::middleware('varbox.check.permissions:permission1,permission2')
            ->get('/_test/check-permissions', function () {
                return 'OK';
            });

        $this->withoutExceptionHandling();

        try {
            $this->actingAs($this->user)->get('/_test/check-permissions');
        } catch (HttpException $e) {
            $this->assertEquals(401, $e->getStatusCode());

            return;
        }

        $this->fail('Expected Symfony\Component\HttpKernel\Exception\HttpException -> 401');
    }

    /** @test */
    public function it_allows_user_with_one_permission()
    {
        $this->user->grantPermission('permission1');

        Route::middleware('varbox.check.permissions:permission1')
            ->get('/_test/check-permissions', function () {
                return 'OK';
            });

        $response = $this->actingAs($this->user)->get('/_test/check-permissions');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    /** @test */
    public function it_allows_user_with_multiple_permissions()
    {
        $this->user->grantPermission([
            'permission1', 'permission2', 'permission3'
        ]);

        Route::middleware('varbox.check.permissions:permission1,permission2,permission3')
            ->get('/_test/check-permissions', function () {
                return 'OK';
            });

        $response = $this->actingAs($this->user)->get('/_test/check-permissions');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    /** @test */
    public function it_allows_super_user_even_without_permissions()
    {
        Role::create([
            'name' => 'Super',
            'guard' => config('auth.defaults.guard')
        ]);

        $this->user->assignRoles('Super');

        Route::middleware('varbox.check.permissions:permission1,permission2,permission3')
            ->get('/_test/check-permissions', function () {
                return 'OK';
            });

        $response = $this->actingAs($this->user)->get('/_test/check-permissions');

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

    /**
     * @return void
     */
    protected function createPermissions()
    {
        for ($i = 1; $i <= 3; $i++) {
            Permission::create([
                'name' => 'permission' . $i,
                'guard' => config('auth.defaults.guard'),
            ]);
        }
    }
}
