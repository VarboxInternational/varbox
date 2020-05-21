<?php

namespace Varbox\Tests\Browser\Seeders;

use Varbox\Contracts\UserModelContract;

class UsersSeeder
{
    /**
     * @return void
     */
    public static function seed()
    {
        $user = app(UserModelContract::class);

        if ($user->where('email', 'admin@mail.com')->count() == 0) {
            $user->doNotLogActivity()->create([
                'name' => 'Admin User',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin'),
                'active' => true,
            ]);
        }
    }
}
