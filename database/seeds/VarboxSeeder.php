<?php

use Illuminate\Database\Seeder;

class VarboxSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(LanguagesSeeder::class);
    }
}
