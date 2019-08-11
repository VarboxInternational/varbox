<?php

return [

    /*
    |
    | All block types available for your application.
    | Whenever you create a new block using the "php artisan varbox:make-block" command or manually, append the type here.
    |
    | --- [Label]:
    | The pretty formatted block type name.
    | This is mainly used inside the admin panel, in places that reference blocks.
    |
    | --- [Composer Class]:
    | The full namespace to the block's view composer.
    | Each block you create will have a "view composer" that's automatically binded to the block's front view namespace.
    | So any logic your block might use, you can define it in the block's view composer class.
    |
    | --- [Views Path]:
    | The full path to the block's views directory.
    | When creating a new block type, besides the "view composer" class, you will also have to views (front & admin).
    |
    | --- [Preview Image]:
    | The name of the image used as block type preview in admin.
    | This should contain the full path to an image of yours inside the "public/" directory.
    | The path is relative to the "public/" directory.
    |
    */
    'types' => [

        'Example' => [
            'label' => 'Example Block',
            'composer_class' => 'App\Blocks\Example\Composer',
            'views_path' => 'app/Blocks/Example/Views',
            'preview_image' => 'vendor/varbox/images/blocks/example.jpg',
        ],

    ],

    /*
    |
    | The config setting below represents "specific upload config options" for the Block model class.
    | The Block uses the HasUploads trait and this options is used to be returned by the "getUploadConfig()" method.
    |
    | If you want to know more about what the "getUploadConfig()" method does, please read the documentation on uploads.
    |
    */
    'upload' => [

        'images' => [
            'styles' => [
                'data[image]' => [
                    'mi_portrait' => [
                        'width' => '300',
                        'height' => '600',
                        'ratio' => true,
                    ],
                    'mi_landscape' => [
                        'width' => '600',
                        'height' => '300',
                        'ratio' => true,
                    ],
                ],
                'data[items][*][image]' => [
                    'mii_portrait' => [
                        'width' => '100',
                        'height' => '400',
                        'ratio' => true,
                    ],
                    'mii_landscape' => [
                        'width' => '400',
                        'height' => '100',
                        'ratio' => true,
                    ],
                ],
            ],
        ],

    ],

];
