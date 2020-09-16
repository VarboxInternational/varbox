<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Varbox\Contracts\StateModelContract;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param StateModelContract $state
     * @param Filesystem $files
     * @return void
     */
    public function run(StateModelContract $state, Filesystem $files)
    {
        if (!$files->exists(database_path('sql/states.sql'))) {
            $this->command->error('The file "database/sql/states.sql" file does not exist!');
            return;
        }

        if ($state->count() > 0) {
            $this->command->error('The "states" database table is not empty!');
            $this->command->warn('For this seed to work, manually delete all entries from your "states" table.');
            return;
        }

        DB::unprepared($files->get(database_path('sql/states.sql')));
    }
}
