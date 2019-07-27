<?php

return [

    /*
    |
    | The max size (in bytes) allowed when uploading a file via the Froala editor.
    | To allow any size, the value below should be null.
    |
    | Default is 5 MB.
    |
    */
    'file_max_size' => 5 * 1024 * 1024,

    /*
    |
    | The allowed extensions when uploading a file via the Froala editor.
    |
    | Specify extensions using an array of values.
    | To allow any extension, the value below should be "null".
    |
    */
    'file_allowed_extensions' => null,

    /*
    |
    | The max size (in bytes) allowed when uploading an image via the Froala editor.
    | To allow any size, the value below should be null.
    |
    | Default is 5 MB.
    |
    */
    'image_max_size' => 5 * 1024 * 1024,

    /*
    |
    | The allowed extensions when uploading an image via the Froala editor.
    |
    | Specify extensions using an array of values.
    | To allow any extension, the value below should be "null".
    |
    */
    'image_allowed_extensions' => [
        'jpeg', 'jpg', 'png', 'gif', 'svg'
    ],

    /*
    |
    | The max size (in bytes) allowed when uploading a video via the Froala editor.
    | To allow any size, the value below should be null.
    |
    | Default is 5 MB.
    |
    */
    'video_max_size' => 5 * 1024 * 1024,

    /*
    |
    | The allowed extensions when uploading a video via the Froala editor.
    |
    | Specify extensions using an array of values.
    | To allow any extension, the value below should be "null".
    |
    */
    'video_allowed_extensions' => [
        'mp4', 'avi', 'flv', 'mov', 'webm', 'ogg', 'mkv'
    ],

];
