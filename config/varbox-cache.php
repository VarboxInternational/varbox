<?php

return [

    'query' => [

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
            | The cache store used for query caching.
            | Please note that because cache tagging is used, "file" or "database" cache drivers are not available here.
            |
            | It's recommended you change the value below with a persistent cache store, such as "redis" or "memcached".
            |
            */
            'store' => 'array',

            /*
            |
            | The value to prefix all query cache tags.
            |
            | This is not the general cache prefix (that is still the value of the key 'prefix' from this file).
            | This value only acts as prefix for query cache tags.
            |
            */
            'prefix' => 'cache.all_query',

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
            | The cache store used for query caching.
            | Please note that because cache tagging is used, "file" or "database" cache drivers are not available here.
            |
            */
            'store' => 'array',

            /*
            |
            | The value to prefix only duplicate query cache tags.
            |
            | This is not the general cache prefix (that is still the value of the key 'prefix' from this file).
            | This value only acts as prefix for query cache tags.
            |
            */
            'prefix' => 'cache.duplicate_query',

        ],

    ],

];
