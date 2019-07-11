<?php

namespace Varbox\Commands;

use Illuminate\Console\Command;
use Varbox\Contracts\BackupModelContract;

class BackupsCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:clean-backups';

    /**
     * Delete activity records older than the config value from "varbox.activity.old_threshold".
     *
     * @var string
     */
    protected $description = 'Remove old records from backups.';

    /**
     * Execute the console command.
     *
     * @param BackupModelContract $backup
     * @return void
     * @throws \Exception
     */
    public function handle(BackupModelContract $backup)
    {
        $days = config('varbox.backup.old_threshold', 30);

        if ((int)$days > 0) {
            $backups = $backup->where('date', '<', today()->subDays($days))->get();
            $count = 0;

            foreach ($backups as $backup) {
                $backup->deleteFromDatabaseAndFilesystem();
                $count++;
            }

            $this->info('Backups cleaned up. ' . $count . ' record(s) were removed.');
        } else {
            $this->line('<fg=red>Could not cleanup the backups because no date threshold is set!</>');
            $this->comment('Please set the "old_threshold" key value in the "config/varbox/backup.php" file.');
        }
    }
}
