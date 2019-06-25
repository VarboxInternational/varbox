<?php

namespace Varbox\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Varbox\Contracts\ActivityModelContract;

class ActivityCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:activity-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old records from the activity log.';

    /**
     * Execute the console command.
     *
     * @param ActivityModelContract $activity
     * @return void
     */
    public function handle(ActivityModelContract $activity)
    {
        $days = config('varbox.varbox-activity.delete_records_older_than', 30);

        if ((int)$days > 0) {
            $count = $activity->where(
                'created_at', '<', Carbon::now()->subDays($days)->format('Y-m-d H:i:s')
            )->delete();

            $this->info('Activity log cleaned up. ' . $count . ' record(s) were removed.');
        } else {
            $this->line('<fg=red>Could not cleanup the activity because no date threshold is set!</>');
            $this->comment('Please set the "delete_records_older_than" key value in the "config/varbox/varbox-activity.php" file.');
        }
    }
}
