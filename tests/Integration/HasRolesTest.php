<?php

namespace Varbox\Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Permission;
use Varbox\Models\Role;
use Varbox\Models\User;

class HasRolesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Role
     */
    protected $role1;

    /**
     * @var Role
     */
    protected $role2;

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
    public function it_can_assign_a_role_by_using_the_model()
    {
        $this->user->assignRoles($this->role1);

        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals($this->role1->id, $this->user->roles()->first()->id);
    }

    /** @test */
    public function it_can_assign_a_role_by_using_the_id()
    {
        $this->user->assignRoles($this->role1->id);

        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals($this->role1->id, $this->user->roles()->first()->id);
    }

    /** @test */
    public function it_can_assign_a_role_by_using_the_name()
    {
        $this->user->assignRoles($this->role1->name);

        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals($this->role1->id, $this->user->roles()->first()->id);
    }

    /** @test */
    public function it_can_assign_multiple_roles_by_using_a_collection()
    {
        $this->user->assignRoles(Role::all());

        $this->assertEquals(2, $this->user->roles()->count());
    }

    /** @test */
    public function it_can_assign_multiple_roles_by_using_an_array_of_ids()
    {
        $this->user->assignRoles([
            $this->role1->id,
            $this->role2->id,
        ]);

        $this->assertEquals(2, $this->user->roles()->count());
    }

    /** @test */
    public function it_can_assign_multiple_roles_by_using_an_array_of_names()
    {
        $this->user->assignRoles([
            $this->role1->name,
            $this->role2->name,
        ]);

        $this->assertEquals(2, $this->user->roles()->count());
    }
    
    /** @test */
    public function it_can_remove_a_role_by_using_the_model()
    {
        $this->user->assignRoles($this->role1);
        $this->user->removeRoles($this->role1);

        $this->assertEquals(0, $this->user->roles()->count());
    }

    /** @test */
    public function it_can_remove_a_role_by_using_the_id()
    {
        $this->user->assignRoles($this->role1->id);
        $this->user->removeRoles($this->role1->id);

        $this->assertEquals(0, $this->user->roles()->count());
    }

    /** @test */
    public function it_can_remove_a_role_by_using_the_name()
    {
        $this->user->assignRoles($this->role1->name);
        $this->user->removeRoles($this->role1->name);

        $this->assertEquals(0, $this->user->roles()->count());
    }

    /** @test */
    public function it_can_remove_multiple_roles_by_using_a_collection()
    {
        $this->user->assignRoles(Permission::all());
        $this->user->removeRoles(Permission::all());

        $this->assertEquals(0, $this->user->roles()->count());
    }

    /** @test */
    public function it_can_remove_multiple_roles_by_using_an_array_of_ids()
    {
        $this->user->assignRoles([
            $this->role1->id, $this->role2->id,
        ]);

        $this->user->removeRoles([
            $this->role1->id, $this->role2->id,
        ]);

        $this->assertEquals(0, $this->user->roles()->count());
    }

    /** @test */
    public function it_can_remove_multiple_roles_by_using_an_array_of_names()
    {
        $this->user->assignRoles([
            $this->role1->name, $this->role2->name,
        ]);

        $this->user->removeRoles([
            $this->role1->name, $this->role2->name,
        ]);

        $this->assertEquals(0, $this->user->roles()->count());
    }

    /** @test */
    public function it_can_sync_a_role_by_using_the_model()
    {
        $this->user->assignRoles(Permission::all());
        $this->user->syncRoles($this->role1);

        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals($this->role1->id, $this->user->roles()->first()->id);
    }

    /** @test */
    public function it_can_sync_a_role_by_using_the_id()
    {
        $this->user->assignRoles(Permission::all());
        $this->user->syncRoles($this->role1->id);

        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals($this->role1->id, $this->user->roles()->first()->id);
    }

    /** @test */
    public function it_can_sync_a_role_by_using_the_name()
    {
        $this->user->assignRoles(Permission::all());
        $this->user->syncRoles($this->role1->name);

        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals($this->role1->id, $this->user->roles()->first()->id);
    }

    /** @test */
    public function it_can_sync_multiple_roles_by_using_a_collection()
    {
        $this->user->assignRoles(Permission::all());
        $this->user->syncRoles(Permission::whereId($this->role1->id)->get());

        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals($this->role1->id, $this->user->roles()->first()->id);
    }

    /** @test */
    public function it_can_sync_multiple_roles_by_using_an_array_of_ids()
    {
        $this->user->assignRoles(Permission::all());
        $this->user->syncRoles([$this->role1->id]);

        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals($this->role1->id, $this->user->roles()->first()->id);
    }

    /** @test */
    public function it_can_sync_multiple_roles_by_using_an_array_of_names()
    {
        $this->user->assignRoles(Permission::all());
        $this->user->syncRoles([$this->role1->name]);

        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals($this->role1->id, $this->user->roles()->first()->id);
    }

    /** @test */
    public function it_can_determine_if_a_user_has_a_certain_role_by_using_the_model()
    {
        $this->user->assignRoles($this->role1);

        $this->assertTrue($this->user->hasRole($this->role1));
        $this->assertFalse($this->user->hasRole($this->role2));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_a_certain_role_by_using_the_id()
    {
        $this->user->assignRoles($this->role1);

        $this->assertTrue($this->user->hasRole($this->role1->id));
        $this->assertFalse($this->user->hasRole($this->role2->id));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_a_certain_role_by_using_the_name()
    {
        $this->user->assignRoles($this->role1);

        $this->assertTrue($this->user->hasRole($this->role1->name));
        $this->assertFalse($this->user->hasRole($this->role2->name));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_any_of_the_roles_supplied_by_using_a_collection()
    {
        $this->user->assignRoles($this->role1);

        $this->assertTrue($this->user->hasAnyRole(Role::all()));
        $this->assertFalse($this->user->hasAnyRole(Role::all()->filter(function ($role) {
            return $role->id != $this->role1->id;
        })));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_any_of_the_roles_supplied_by_using_an_array_of_ids()
    {
        $this->user->assignRoles($this->role1);

        $this->assertTrue($this->user->hasAnyRole([$this->role1->id, $this->role2->id]));
        $this->assertFalse($this->user->hasAnyRole([$this->role2->id]));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_any_of_the_roles_supplied_by_using_an_array_of_names()
    {
        $this->user->assignRoles($this->role1);

        $this->assertTrue($this->user->hasAnyRole([$this->role1->name, $this->role2->name]));
        $this->assertFalse($this->user->hasAnyRole([$this->role2->name]));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_all_of_the_roles_supplied_by_using_a_collection()
    {
        $this->user->assignRoles($this->role1);

        $this->assertTrue($this->user->hasAllRoles(Role::whereName($this->role1->name)->get()));
        $this->assertFalse($this->user->hasAllRoles(Role::all()));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_all_of_the_roles_supplied_by_using_an_array_of_ids()
    {
        $this->user->assignRoles($this->role1);

        $this->assertTrue($this->user->hasAllRoles([$this->role1->id]));
        $this->assertFalse($this->user->hasAllRoles([$this->role1->id, $this->role2->id]));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_all_of_the_roles_supplied_by_using_an_array_of_names()
    {
        $this->user->assignRoles($this->role1);

        $this->assertTrue($this->user->hasAllRoles([$this->role1->name]));
        $this->assertFalse($this->user->hasAllRoles([$this->role1->name, $this->role2->name]));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_a_certain_permission_by_using_the_model()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertTrue($this->user->hasPermission($this->permission1));
        $this->assertFalse($this->user->hasPermission($this->permission2));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_a_certain_permission_by_using_the_id()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertTrue($this->user->hasPermission($this->permission1->id));
        $this->assertFalse($this->user->hasPermission($this->permission2->id));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_a_certain_permission_by_using_the_name()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertTrue($this->user->hasPermission($this->permission1->name));
        $this->assertFalse($this->user->hasPermission($this->permission2->name));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_any_of_the_permissions_supplied_by_using_a_collection()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertTrue($this->user->hasAnyPermission(Permission::all()));
        $this->assertFalse($this->user->hasAnyPermission(Permission::all()->filter(function ($permission) {
            return $permission->id != $this->permission1->id;
        })));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_any_of_the_permissions_supplied_by_using_an_array_of_ids()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertTrue($this->user->hasAnyPermission([$this->permission1->id, $this->permission2->id]));
        $this->assertFalse($this->user->hasAnyPermission([$this->permission2->id]));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_any_of_the_permissions_supplied_by_using_an_array_of_names()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertTrue($this->user->hasAnyPermission([$this->permission1->name, $this->permission2->name]));
        $this->assertFalse($this->user->hasAnyPermission([$this->permission2->name]));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_all_of_the_permissions_supplied_by_using_a_collection()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertTrue($this->user->hasAllPermissions(Permission::whereName($this->permission1->name)->get()));
        $this->assertFalse($this->user->hasAllPermissions(Permission::all()));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_all_of_the_permissions_supplied_by_using_an_array_of_ids()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertTrue($this->user->hasAllPermissions([$this->permission1->id]));
        $this->assertFalse($this->user->hasAllPermissions([$this->permission1->id, $this->permission2->id]));
    }

    /** @test */
    public function it_can_determine_if_a_user_has_all_of_the_permissions_supplied_by_using_an_array_of_names()
    {
        $this->user->grantPermission($this->permission1);

        $this->assertTrue($this->user->hasAllPermissions([$this->permission1->name]));
        $this->assertFalse($this->user->hasAllPermissions([$this->permission1->name, $this->permission2->name]));
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

        $this->role1 = Role::create([
            'name' => 'test-role-1',
            'guard' => config('auth.defaults.guard', 'web'),
        ]);

        $this->role2 = Role::create([
            'name' => 'test-role-2',
            'guard' => config('auth.defaults.guard', 'web'),
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
