<?php

namespace Varbox\Tests\Integration;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Permission;
use Varbox\Models\Role;
use Varbox\Models\User;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class PermissionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Permission
     */
    protected $permission;

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
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Permission::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Permission::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Permission::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Permission::class));
    }

    /** @test */
    public function it_belongs_to_many_users()
    {
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'email' => 'test-' . $i . '@mail.com',
                'password' => bcrypt('pa55word'),
            ]);

            $this->permission->users()->attach($user->id);
        }

        $this->assertTrue($this->permission->users() instanceof BelongsToMany);
        $this->assertEquals(3, $this->permission->users()->count());
    }

    /** @test */
    public function it_belongs_to_many_roles()
    {
        for ($i = 1; $i <= 3; $i++) {
            $role = Role::create([
                'name' => 'test-permission-' . $i,
                'guard' => config('auth.defaults.guard', 'web'),
            ]);

            $this->permission->roles()->attach($role->id);
        }

        $this->assertTrue($this->permission->roles() instanceof BelongsToMany);
        $this->assertEquals(3, $this->permission->roles()->count());
    }

    /** @test */
    public function it_can_get_a_permission_by_id()
    {
        $permission = (new Permission)->getPermission($this->permission->id);

        $this->assertEquals($this->permission->id, $permission->id);
        $this->assertEquals($this->permission->name, $permission->name);
    }

    /** @test */
    public function it_can_get_a_permission_by_name()
    {
        $permission = (new Permission)->getPermission($this->permission->name);

        $this->assertEquals($this->permission->id, $permission->id);
        $this->assertEquals($this->permission->name, $permission->name);
    }

    /** @test */
    public function it_can_get_permissions_by_an_array_of_ids()
    {
        $permissions = Permission::getPermissions([$this->permission->id]);

        foreach ($permissions as $index => $permission) {
            $this->assertEquals($this->permission->id, $permission->id);
            $this->assertEquals($this->permission->name, $permission->name);
        }
    }

    /** @test */
    public function it_can_get_permissions_by_an_array_of_names()
    {
        $permissions = Permission::getPermissions([$this->permission->name]);

        foreach ($permissions as $index => $permission) {
            $this->assertEquals($this->permission->id, $permission->id);
            $this->assertEquals($this->permission->name, $permission->name);
        }
    }

    /** @test */
    public function it_can_find_a_permission_by_name()
    {
        $permission = Permission::findByName($this->permission->name);

        $this->assertEquals($this->permission->name, $permission->name);
    }

    /** @expectedException ModelNotFoundException */
    public function it_throws_exception_when_it_cannot_find_a_permission_by_name()
    {
        Permission::findByName('different-name');
    }

    /**
     * Create a permission instance.
     *
     * @return void
     */
    protected function setUpTestingConditions()
    {
        $this->permission = Permission::create([
            'name' => 'test-permission',
            'guard' => config('auth.defaults.guard', 'web'),
        ]);
    }
}
