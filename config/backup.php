<?php

/*
|
| This config file is derived from "spatie/laravel-backup": https://github.com/spatie/laravel-backup
|
| It only contains the most important config options of the backup feature.
| For more control, please publish the "spatie/laravel-backup" config file.
|
*/
return [

    /*
    |
    | The name of this application. You can use this name to monitor the backups.
    |
    */
    'name' => env('APP_NAME'),

    /*
    |
    | The backup source options.
    |
    */
    'source' => [

        /*
        |
        | The backup file options.
        |
        */
        'files' => [

            /*
            |
            | The list of directories and files that will be included in the backup.
            |
            */
            'include' => [
                base_path(),
            ],

            /*
            | These directories and files will be excluded from the backup.
            | Directories used by the backup process will automatically be excluded.
            */
            'exclude' => [
                base_path('vendor'),
                base_path('node_modules'),
            ],

            /*
            |
            | Flag indicating whether or not symlinks should be followed.
            |
            */
            'follow_links' => false,
        ],

        /*
        |
        | The names of the connections to the databases that should be backed up.
        | MySQL, PostgreSQL, SQLite and Mongo databases are supported.
        |
        */
        'databases' => [
            'mysql',
        ],

    ],

    /*
    |
    | The backup destination options.
    |
    */
    'destination' => [

        /*
        |
        | The filename prefix used for the backup zip file.
        |
        */
        'filename_prefix' => 'backup_',

        /*
        |
        | The disk names on which the backups will be stored.
        |
        */
        'disks' => [
            'backups',
        ],
    ],

    /*
    |
    | You can get notified when specific events occur.
    |
    | You can also use your own notification classes.
    | Just make sure the class is named after one of the "Spatie\Backup\Events" classes.
    |
    */
    'notifications' => [
        /*
        |
        | The notification classes and their method of notification.
        |
        */
        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailed::class => [/*'mail'*/],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFound::class => [/*'mail'*/],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailed::class => [/*'mail'*/],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessful::class => [/*'mail'*/],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFound::class => [/*'mail'*/],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessful::class => [/*'mail'*/],
        ],

        /*
        |
        | The email address where to send the mail notifications.
        |
        */
        'email' => '',
    ],

    /*
    |
    | The database dump can be compressed to decrease disk space usage.
    | If you do not want any compressor at all, set it to "null".
    |
    */
    'database_dump_compressor' => Spatie\DbDumper\Compressors\GzipCompressor::class,

    /*
    |
    | This option accepts an integer, representing the number of days.
    |
    | This option is used to delete backups older than the number of days supplied when:
    | - executing the cli command: "php artisan varbox:clean-backups"
    | - clicking the "Delete Old Activity" button from the admin panel, inside the activity list view
    |
    | If set to "null" or "0", no past activities will be deleted whatsoever.
    |
    */
    'old_threshold' => 30,

];
