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
    | --- [View]:
    | The view to be used for pages on the front-end.
    | The view path is relative to the "resources/views/" directory.
    |
    */
    'types' => [

        'default' => [
            'controller' => 'PagesController',
            'action' => 'normal',
            'view' => 'pages.normal',
        ],

        'home' => [
            'controller' => 'PagesController',
            'action' => 'home',
            'view' => 'pages.home',
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
