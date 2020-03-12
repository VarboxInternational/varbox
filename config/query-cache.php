<?php

return [

    /*
    |
    | The below configuration options are for caching absolutely all queries, forever.
    |
    */
    'all' => [
        /*
        |
        | Flag indicating whether or not query caching should run (forever cache).
        | By default, it's set to "false". If you want to enable it, set the "CACHE_ALL_QUERIES=true" in your .env file.
        |
        */
        'enabled' => env('CACHE_ALL_QUERIES', false),

        /*
        |
        | The value to prefix all query cache tags.
        |
        | This is not the general cache prefix.
        | This value only acts as prefix for query cache tags.
        |
        */
        'prefix' => 'cache.all_queries',

    ],

    /*
    |
    | The below configuration options are for caching only duplicate queries that might exist for a request.
    |
    */
    'duplicate' => [

        /*
        |
        | Flag indicating whether or not query caching only on duplicate queries should run (only for the current request).
        | By default, it's set to "false". If you want to enable it, set the "CACHE_DUPLICATE_QUERIES=true" in your .env file.
        |
        */
        'enabled' => env('CACHE_DUPLICATE_QUERIES', false),

        /*
        |
        | The value to prefix only duplicate query cache tags.
        |
        | This is not the general cache prefix.
        | This value only acts as prefix for query cache tags.
        |
        */
        'prefix' => 'cache.duplicate_queries',

    ],

];
