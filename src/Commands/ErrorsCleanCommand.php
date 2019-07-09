<?php

namespace Varbox\Commands;

use Illuminate\Console\Command;
use Varbox\Contracts\ErrorModelContract;

class ErrorsCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:clean-errors';

    /**
     * Delete activity records older than the config value from "varbox.errors.old_threshold".
     *
     * @var string
     */
    protected $description = 'Remove old records from the errors table.';

    /**
     * Execute the console command.
     *
     * @param ErrorModelContract $error
     * @return void
     */
    public function handle(ErrorModelContract $error)
    {
        $days = config('varbox.errors.old_threshold', 30);

        if ((int)$days > 0) {
            $count = $error->where('created_at', '<', today()->subDays($days))->delete();

            $this->info('Errors cleaned up. ' . $count . ' record(s) were removed.');
        } else {
            $this->line('<fg=red>Could not cleanup the errors because no date threshold is set!</>');
            $this->comment('Please set the "old_threshold" key value in the "config/varbox/errors.php" file.');
        }
    }
}
