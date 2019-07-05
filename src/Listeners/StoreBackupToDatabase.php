<?php

namespace Varbox\Listeners;

use Spatie\Backup\Events\BackupWasSuccessful;
use Varbox\Contracts\BackupModelContract;

class StoreBackupToDatabase
{
    /**
     * The backup model instance.
     *
     * @var BackupModelContract
     */
    protected $model;

    /**
     * StoreBackupToDatabase constructor.
     *
     * @param BackupModelContract $model
     */
    public function __construct(BackupModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * Handle the event.
     *
     * @param BackupWasSuccessful $event
     * @return void
     */
    public function handle(BackupWasSuccessful $event)
    {
        $destination = $event->backupDestination;
        $backup = $destination->newestBackup();

        $this->model->create([
            'name' => $destination->backupName(),
            'disk' => $destination->diskName(),
            'path' => $backup->path(),
            'date' => $backup->date(),
            'size' => $backup->size(),
        ]);
    }
}