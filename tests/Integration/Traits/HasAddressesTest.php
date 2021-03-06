<?php

namespace Varbox\Tests\Integration\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\User;
use Varbox\Tests\Integration\TestCase;

class HasAddressesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    /** @test */
    public function it_has_many_addresses()
    {
        $this->createUser();

        for ($i = 1; $i <= 3; $i++) {
            $this->user->addresses()->create([
                'address' => 'address ' . $i
            ]);
        }

        $this->assertTrue($this->user->addresses() instanceof HasMany);
        $this->assertEquals(3, $this->user->addresses()->count());
    }

    /**
     * @return $this
     */
    protected function createUser()
    {
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test-user@mail.com',
            'password' => bcrypt('test_password'),
        ]);

        return $this;
    }
}
