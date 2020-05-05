<?php

return [

    /*
    |
    | All page types available for your application.
    |
    | --- ARRAY KEY:
    | The actual page type name.
    | This will be persisted to the pages database table.
    |
    | --- [Controller]:
    | The controller to be used for pages on the front-end.
    |
    | --- [Action]:
    | The action from the controller to be used for pages on the front-end.
    |
    | --- [Locations]:
    | The locations in page available for inserting blocks in.
    |
    */
    'types' => [

        'default' => [
            'controller' => '\App\Http\Controllers\PagesController',
            'action' => 'show',
            'locations' => [
                'header', 'content', 'footer'
            ]
        ],

    ],

    /*
    |
    | The config setting below represents "specific upload config options" for the Page model class.
    | The Page uses the HasUploads trait and this options is used to be returned by the "getUploadConfig()" method.
    |
    | If you want to know more about what the "getUploadConfig()" method does, please read the documentation on uploads.
    |
    */
    'upload' => [

        /*'images' => [
            'styles' => [
                'data[image]' => [
                    'portrait' => [
                        'width' => '100',
                        'height' => '300',
                        'ratio' => true,
                    ],
                    'landscape' => [
                        'width' => '300',
                        'height' => '100',
                        'ratio' => true,
                    ],
                ],
            ],
        ],*/

    ],

];
