<?php

namespace Varbox\Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Permission;
use Varbox\Models\Role;
use Varbox\Models\User;

class HasPermissionsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Permission
     */
    protected $permission1;

    /**
     * @var Permission
     */
    protected $permission2;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpTestingConditions();
    }

    /** @test */
    public function it_can_grant_a_permission_by_using_the_model()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertEquals(1, $this->user->permissions()->count());
        $this->assertEquals($this->permission1->id, $this->user->permissions()->first()->id);
    }

    /** @test */
    public function it_can_grant_a_permission_by_using_the_id()
    {
        $this->user->grantPermission($this->permission1->id);

        $this->assertEquals(1, $this->user->permissions()->count());
        $this->assertEquals($this->permission1->id, $this->user->permissions()->first()->id);
    }

    /** @test */
    public function it_can_grant_a_permission_by_using_the_name()
    {
        $this->user->grantPermission($this->permission1->name);

        $this->assertEquals(1, $this->user->permissions()->count());
        $this->assertEquals($this->permission1->id, $this->user->permissions()->first()->id);
    }

    /** @test */
    public function it_can_grant_multiple_permissions_by_using_a_collection()
    {
        $this->user->grantPermission(Permission::all());

        $this->assertEquals(2, $this->user->permissions()->count());
    }

    /** @test */
    public function it_can_grant_multiple_permissions_by_using_an_array_of_ids()
    {
        $this->user->grantPermission([
            $this->permission1->id,
            $this->permission2->id,
        ]);

        $this->assertEquals(2, $this->user->permissions()->count());
    }

    /** @test */
    public function it_can_grant_multiple_permissions_by_using_an_array_of_names()
    {
        $this->user->grantPermission([
            $this->permission1->name,
            $this->permission2->name,
        ]);

        $this->assertEquals(2, $this->user->permissions()->count());
    }

    /** @test */
    public function it_can_revoke_a_permission_by_using_the_model()
    {
        $this->user->grantPermission($this->permission1);
        $this->user->revokePermission($this->permission1);

        $this->assertEquals(0, $this->user->permissions()->count());
    }

    /** @test */
    public function it_can_revoke_a_permission_by_using_the_id()
    {
        $this->user->grantPermission($this->permission1->id);
        $this->user->revokePermission($this->permission1->id);

        $this->assertEquals(0, $this->user->permissions()->count());
    }

    /** @test */
    public function it_can_revoke_a_permission_by_using_the_name()
    {
        $this->user->grantPermission($this->permission1->name);
        $this->user->revokePermission($this->permission1->name);

        $this->assertEquals(0, $this->user->permissions()->count());
    }

    /** @test */
    public function it_can_revoke_multiple_permissions_by_using_a_collection()
    {
        $this->user->grantPermission(Permission::all());
        $this->user->revokePermission(Permission::all());

        $this->assertEquals(0, $this->user->permissions()->count());
    }

    /** @test */
    public function it_can_revoke_multiple_permissions_by_using_an_array_of_ids()
    {
        $this->user->grantPermission([
            $this->permission1->id, $this->permission2->id,
        ]);

        $this->user->revokePermission([
            $this->permission1->id, $this->permission2->id,
        ]);

        $this->assertEquals(0, $this->user->permissions()->count());
    }

    /** @test */
    public function it_can_revoke_multiple_permissions_by_using_an_array_of_names()
    {
        $this->user->grantPermission([
            $this->permission1->name, $this->permission2->name,
        ]);

        $this->user->revokePermission([
            $this->permission1->name, $this->permission2->name,
        ]);

        $this->assertEquals(0, $this->user->permissions()->count());
    }

    /** @test */
    public function it_can_sync_a_permission_by_using_the_model()
    {
        $this->user->grantPermission(Permission::all());
        $this->user->syncPermissions($this->permission1);

        $this->assertEquals(1, $this->user->permissions()->count());
        $this->assertEquals($this->permission1->id, $this->user->permissions()->first()->id);
    }

    /** @test */
    public function it_can_sync_a_permission_by_using_the_id()
    {
        $this->user->grantPermission(Permission::all());
        $this->user->syncPermissions($this->permission1->id);

        $this->assertEquals(1, $this->user->permissions()->count());
        $this->assertEquals($this->permission1->id, $this->user->permissions()->first()->id);
    }

    /** @test */
    public function it_can_sync_a_permission_by_using_the_name()
    {
        $this->user->grantPermission(Permission::all());
        $this->user->syncPermissions($this->permission1->name);

        $this->assertEquals(1, $this->user->permissions()->count());
        $this->assertEquals($this->permission1->id, $this->user->permissions()->first()->id);
    }

    /** @test */
    public function it_can_sync_multiple_permissions_by_using_a_collection()
    {
        $this->user->grantPermission(Permission::all());
        $this->user->syncPermissions(Permission::whereId($this->permission1->id)->get());

        $this->assertEquals(1, $this->user->permissions()->count());
        $this->assertEquals($this->permission1->id, $this->user->permissions()->first()->id);
    }

    /** @test */
    public function it_can_sync_multiple_permissions_by_using_an_array_of_ids()
    {
        $this->user->grantPermission(Permission::all());
        $this->user->syncPermissions([$this->permission1->id]);

        $this->assertEquals(1, $this->user->permissions()->count());
        $this->assertEquals($this->permission1->id, $this->user->permissions()->first()->id);
    }

    /** @test */
    public function it_can_sync_multiple_permissions_by_using_an_array_of_names()
    {
        $this->user->grantPermission(Permission::all());
        $this->user->syncPermissions([$this->permission1->name]);

        $this->assertEquals(1, $this->user->permissions()->count());
        $this->assertEquals($this->permission1->id, $this->user->permissions()->first()->id);
    }

    /**
     * Create a permission instance.
     *
     * @return void
     */
    protected function setUpTestingConditions()
    {
        $this->user = User::create([
            'email' => 'test@mail.com',
            'password' => bcrypt('pa55word'),
        ]);

        $this->permission1 = Permission::create([
            'name' => 'test-permission-1',
            'guard' => config('auth.defaults.guard', 'web'),
        ]);

        $this->permission2 = Permission::create([
            'name' => 'test-permission-2',
            'guard' => config('auth.defaults.guard', 'web'),
        ]);
    }
}
