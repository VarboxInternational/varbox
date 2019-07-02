<?php

namespace Varbox\Tests\Integration;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Permission;
use Varbox\Models\Role;
use Varbox\Models\User;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasPermissions;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class RoleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Role
     */
    protected $role;

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
    public function it_uses_the_has_permissions_trait()
    {
        $this->assertArrayHasKey(HasPermissions::class, class_uses(Role::class));
    }

    /** @test */
    public function it_uses_the_has_activity_trait()
    {
        $this->assertArrayHasKey(HasActivity::class, class_uses(Role::class));
    }

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Role::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Role::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Role::class));
    }

    /** @test */
    public function it_belongs_to_many_users()
    {
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'email' => 'test-' . $i . '@mail.com',
                'password' => bcrypt('pa55word'),
            ]);

            $this->role->users()->attach($user->id);
        }

        $this->assertTrue($this->role->users() instanceof BelongsToMany);
        $this->assertEquals(3, $this->role->users()->count());
    }

    /** @test */
    public function it_belongs_to_many_permissions()
    {
        for ($i = 1; $i <= 3; $i++) {
            $permission = Permission::create([
                'name' => 'test-permission-' . $i,
                'guard' => config('auth.defaults.guard', 'web'),
            ]);

            $this->role->permissions()->attach($permission->id);
        }

        $this->assertTrue($this->role->permissions() instanceof BelongsToMany);
        $this->assertEquals(3, $this->role->permissions()->count());
    }

    /** @test */
    public function it_can_get_a_role_by_id()
    {
        $role = (new Role)->getRole($this->role->id);

        $this->assertEquals($this->role->id, $role->id);
        $this->assertEquals($this->role->name, $role->name);
    }

    /** @test */
    public function it_can_get_a_role_by_name()
    {
        $role = (new Role)->getRole($this->role->name);

        $this->assertEquals($this->role->id, $role->id);
        $this->assertEquals($this->role->name, $role->name);
    }

    /** @test */
    public function it_can_get_roles_by_an_array_of_ids()
    {
        $roles = (new Role)->getRoles([$this->role->id]);

        foreach ($roles as $index => $role) {
            $this->assertEquals($this->role->id, $role->id);
            $this->assertEquals($this->role->name, $role->name);
        }
    }

    /** @test */
    public function it_can_get_roles_by_an_array_of_names()
    {
        $roles = (new Role)->getRoles([$this->role->name]);

        foreach ($roles as $index => $role) {
            $this->assertEquals($this->role->id, $role->id);
            $this->assertEquals($this->role->name, $role->name);
        }
    }

    /** @test */
    public function it_can_find_a_role_by_name()
    {
        $role = Role::findByName($this->role->name);

        $this->assertEquals($this->role->name, $role->name);
    }

    /** @expectedException ModelNotFoundException */
    public function it_throws_exception_when_it_cannot_find_a_role_by_name()
    {
        Role::findByName('different-name');
    }

    /**
     * Set up testing conditions for roles.
     *
     * @return void
     */
    protected function setUpTestingConditions()
    {
        $this->role = Role::create([
            'name' => 'test-role',
            'guard' => config('auth.defaults.guard', 'web'),
        ]);
    }
}
