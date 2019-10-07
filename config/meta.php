<?php

return [

    /*
    |
    | Here you can specify globally default values for specific meta tags.
    |
    */
    'default_values' => [

        'title' => env('APP_NAME', ''),

    ],


    /*
    |
    | All of the available meta tags (includes meta, og and twitter).
    | If you'll try to set values for meta tags that are not specified below, they will not appear.
    |
    */
    'available_tags' => [

        /*
        |
        | The available default meta tags.
        |
        */
        'meta' => [

            'title',
            'description',
            'robots',

        ],

        /*
        |
        | The available open graph tags..
        |
        */
        'og' => [

            'og:title',
            'og:type',
            'og:url',
            'og:image',
            'og:audio',
            'og:video',
            'og:description',
            'og:site_name',
            'og:determiner',
            'og:locale',
            'og:locale:alternate',

        ],

        /*
        |
        | The available twitter card tags.
        |
        */
        'twitter' => [

            'twitter:card',
            'twitter:site',
            'twitter:site:id',
            'twitter:creator',
            'twitter:creator:id',
            'twitter:description',
            'twitter:title',
            'twitter:image',
            'twitter:image:alt',
            'twitter:player',
            'twitter:player:width',
            'twitter:player:height',
            'twitter:player:stream',
            'twitter:app:name:iphone',
            'twitter:app:id:iphone',
            'twitter:app:url:iphone',
            'twitter:app:name:ipad',
            'twitter:app:id:ipad',
            'twitter:app:url:ipad',
            'twitter:app:name:googleplay',
            'twitter:app:id:googleplay',
            'twitter:app:url:googleplay',

        ]

    ],

];
