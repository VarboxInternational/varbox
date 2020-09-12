<?php

namespace Database\Seeders;

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
            $admin = $user->doNotLogActivity()->create([
                'name' => 'Admin User',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin'),
                'active' => true,
            ]);

            $admin->doNotLogActivity()->assignRoles([
                'Admin', 'Super'
            ]);
        }
    }
}
