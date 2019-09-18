<?php

return [

    /*
    |
    | The view id of which you want to display data.
    |
    | This is derived from "spatie/laravel-analytics": https://github.com/spatie/laravel-analytics
    |
    */
    'view_id' => env('ANALYTICS_VIEW_ID'),

    /*
    |
    | Path to the client secret json file.
    | Take a look at the README of this package to learn how to get this file.
    |
    | This is derived from "spatie/laravel-analytics": https://github.com/spatie/laravel-analytics
    |
    */
    'credentials_json' => storage_path('app/analytics/service-account-credentials.json'),

    /*
    |
    | Cache specific configurations.
    |
    */
    'cache' => [

        /*
        |
        | The cache store used for storing Google API responses.
        |
        | This is derived from "spatie/laravel-analytics": https://github.com/spatie/laravel-analytics
        |
        */
        'store' => 'file',

        /*
        |
        | The amount of minutes the Google API responses will be cached.
        | If you set this to zero, the responses won't be cached at all.
        |
        | This is derived from "spatie/laravel-analytics": https://github.com/spatie/laravel-analytics
        |
        */
        'lifetime' => 60 * 24,
    ],
];
