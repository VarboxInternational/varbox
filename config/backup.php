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
    | The database dump can be compressed to decrease disk space usage.
    | If you do not want any compressor at all, set it to "null".
    |
    */
    'database_dump_compressor' => Spatie\DbDumper\Compressors\GzipCompressor::class,

    'cleanup' => [
        /*
        |
        | The strategy that will be used to cleanup old backups. The default strategy
        | will keep all backups for a certain amount of days. After that period only
        | a daily backup will be kept. After that period only weekly backups will
        | be kept and so on.
        |
        | No matter how you configure it the default strategy will never
        | delete the newest backup.
        |
        */
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [
            /*
            |
            | The number of days for which backups must be kept.
            |
            */
            'keep_all_backups_for_days' => 7,

            /*
            |
            | The number of days for which daily backups must be kept.
            |
            */
            'keep_daily_backups_for_days' => 16,

            /*
            |
            | The number of weeks for which one weekly backup must be kept.
            |
            */
            'keep_weekly_backups_for_weeks' => 8,

            /*
            |
            | The number of months for which one monthly backup must be kept.
            |
            */
            'keep_monthly_backups_for_months' => 4,

            /*
            |
            | The number of years for which one yearly backup must be kept.
            |
            */
            'keep_yearly_backups_for_years' => 2,

            /*
            |
            | After cleaning up the backups remove the oldest backup until this amount of megabytes has been reached.
            |
            */
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],

];
