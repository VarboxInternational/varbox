<?php

namespace Varbox\Tests\Integration;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\User;

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
        $this->createPost();

        for ($i = 1; $i <= 3; $i++) {
            $this->user->addresses()->create([
                'address' => 'address ' . $i
            ]);
        }

        $this->assertTrue($this->user->addresses() instanceof HasMany);
        $this->assertEquals(3, $this->user->addresses()->count());
    }

    /**
     * Create a user instance.
     *
     * @return $this
     */
    protected function createPost()
    {
        $this->user = User::create([
            'email' => 'test-user@mail.com',
            'password' => bcrypt('test_password'),
        ]);

        return $this;
    }
}
