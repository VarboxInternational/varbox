<?php

namespace Varbox\Seed;

use Illuminate\Database\Seeder;
use Varbox\Contracts\UserModelContract;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param UserModelContract $user
     * @return void
     */
    public function run(UserModelContract $user)
    {
        if ($user->where('email', 'admin@mail.com')->count() == 0) {
            $admin = $user->create([
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin'),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'active' => true,
            ]);

            $admin->assignRoles([
                'Admin', 'Super'
            ]);
        }
    }
}
