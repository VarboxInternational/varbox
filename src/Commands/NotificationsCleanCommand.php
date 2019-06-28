<?php

namespace Varbox\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:clean-notifications';

    /**
     * Delete notification records older than the config value from "varbox.varbox-notification.old_threshold".
     *
     * @var string
     */
    protected $description = 'Remove old records from the notifications.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = config('varbox.varbox-notification.old_threshold', 30);

        if ((int)$days > 0) {
            $count = DatabaseNotification::where(
                'created_at', '<', Carbon::now()->subDays($days)->format('Y-m-d H:i:s')
            )->delete();

            $this->info('Notifications cleaned up. ' . $count . ' record(s) were removed.');
        } else {
            $this->line('<fg=red>Could not cleanup the notifications because no date threshold is set!</>');
            $this->comment('Please set the "old_threshold" key value in the "config/varbox/varbox-notification.php" file.');
        }
    }
}
