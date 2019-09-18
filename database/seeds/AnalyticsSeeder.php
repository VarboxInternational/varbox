<?php

namespace Varbox\Seed;

use Illuminate\Database\Seeder;
use Varbox\Contracts\AnalyticsModelContract;

class AnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param AnalyticsModelContract $analytics
     * @return void
     */
    public function run(AnalyticsModelContract $analytics)
    {
        if ($analytics->count() == 0) {
            $analytics->create([
                'code' => null
            ]);
        }
    }
}
