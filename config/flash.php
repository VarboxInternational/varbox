<?php

return [

    /*
    |
    | These are the "flash types" supported by the flash helper so far (different designs/structures for the flash message functionality).
    | Each of these types have a corresponding view file inside the "/resources/views/vendor/varbox/helpers/flash".
    | The name for each type is "/resources/views/vendor/varbox/helpers/flash/{TYPE_FROM_BELOW}.blade.php".
    |
    | If you would like to have other flash types, meaning additional styled flash messages,
    | just add a new value here and create the corresponding blade view inside "/resources/views/vendor/varbox/helpers/flash".
    |
    */
    'types' => [

        'default',
        'admin',

    ],

    /*
    |
    | Flag whether or not to log any error passed to the "error()" or "warning()" methods.
    | It's recommended that you leave this set to "true", so the errors are actually logged and the developers can easily debug.
    |
    | The errors are logged inside the "laravel.log" file, just like any other Laravel exception.
    |
    */
    'log_errors' => true,

];
