<?php

namespace Varbox\Tests\Integration;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Role;
use Varbox\Models\User;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

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
    public function it_can_return_the_full_name()
    {
        $this->assertEquals('Test User', $this->user->full_name);
    }

    /** @test */
    public function it_can_filter_only_active_users()
    {
        $this->assertEquals(1, User::onlyActive()->count());
    }

    /** @test */
    public function it_can_filter_only_inactive_users()
    {
        $this->assertEquals(0, User::onlyInactive()->count());
    }

    /** @test */
    public function it_can_filter_only_admin_users()
    {
        Role::create([
            'name' => 'Admin',
            'guard' => config('auth.defaults.guard', 'web')
        ]);

        $this->user->assignRoles('Admin');

        $this->assertEquals(1, User::onlyAdmins()->count());
    }

    /** @test */
    public function it_can_filter_excluding_admin_users()
    {
        Role::create([
            'name' => 'Admin',
            'guard' => config('auth.defaults.guard', 'web')
        ]);

        $this->user->removeRoles('Admin');

        $this->assertEquals(1, User::excludingAdmins()->count());
    }

    /** @test */
    public function it_can_filter_only_super_users()
    {
        Role::create([
            'name' => 'Super',
            'guard' => config('auth.defaults.guard', 'web')
        ]);

        $this->user->assignRoles('Super');

        $this->assertEquals(1, User::onlySuper()->count());
    }

    /** @test */
    public function it_can_filter_excluding_super_users()
    {
        Role::create([
            'name' => 'Super',
            'guard' => config('auth.defaults.guard', 'web')
        ]);

        $this->user->removeRoles('Super');

        $this->assertEquals(1, User::excludingSuper()->count());
    }

    /** @test */
    public function it_determines_if_a_user_is_active()
    {
        $this->user->update(['active' => true]);

        $this->assertTrue($this->user->isActive());

        $this->user->update(['active' => false]);

        $this->assertFalse($this->user->isActive());
    }

    /** @test */
    public function it_determines_if_a_user_is_inactive()
    {
        $this->user->update(['active' => false]);

        $this->assertTrue($this->user->isInactive());

        $this->user->update(['active' => true]);

        $this->assertFalse($this->user->isInactive());
    }

    /** @test */
    public function it_determines_if_a_user_is_an_admin_user()
    {
        Role::create([
            'name' => 'Admin',
            'guard' => config('auth.defaults.guard', 'web')
        ]);

        $this->user->assignRoles('Admin');

        $this->assertTrue($this->user->isAdmin());
    }

    /** @test */
    public function it_determines_if_a_user_is_a_super_user()
    {
        Role::create([
            'name' => 'Super',
            'guard' => config('auth.defaults.guard', 'web')
        ]);

        $this->user->assignRoles('Super');

        $this->assertTrue($this->user->isSuper());
    }
    /**
     * Create a user instance.
     *
     * @return void
     */
    protected function setUpTestingConditions()
    {
        $this->user = User::create([
            'email' => 'test@mail.com',
            'password' => bcrypt('pa55word'),
            'first_name' => 'Test',
            'last_name' => 'User',
            'active' => true,
        ]);
    }
}
