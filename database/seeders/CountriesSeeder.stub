<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Varbox\Contracts\CountryModelContract;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param CountryModelContract $country
     * @param Filesystem $files
     * @return void
     */
    public function run(CountryModelContract $country, Filesystem $files)
    {
        if (!$files->exists(database_path('sql/countries.sql'))) {
            $this->command->error('The file "database/sql/countries.sql" file does not exist!');
            return;
        }

        if ($country->count() > 0) {
            $this->command->error('The "countries" database table is not empty!');
            $this->command->warn('For this seed to work, manually delete all entries from your "countries" table.');
            return;
        }

        DB::unprepared($files->get(database_path('sql/countries.sql')));
    }
}
