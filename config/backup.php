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
        'follow_links' => true,
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

    /*
    |
    | The database dump can be gzipped to decrease disk space usage.
    |
    */
    'gzip_database_dump' => true,

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

];
