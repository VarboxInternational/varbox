<?php

return [

    /*
    |
    | The max size (in bytes) allowed when uploading an image via the WYSIWYG editor.
    | To allow any size, the value below should be null.
    |
    | Default is 5 MB.
    |
    */
    'image_max_size' => 2 * 1024 * 1024,

    /*
    |
    | The allowed extensions when uploading an image via the WYSIWYG editor.
    |
    | Specify extensions using an array of values.
    | To allow any extension, the value below should be "null".
    |
    */
    'image_allowed_extensions' => [
        'jpeg', 'jpg', 'png',
    ],

];
