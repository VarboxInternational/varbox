<?php

return [

    /*
    |
    | These are the "validation types" supported by the validation helper so far (different designs/structures for the validation errors).
    | Each of these types have a corresponding view file inside the "/resources/views/vendor/varbox/helpers/validation/errors".
    | The name for each type is "/resources/views/vendor/varbox/helpers/validation/errors/{TYPE_FROM_BELOW}.blade.php".
    |
    | If you would like to have other validation types, meaning additional styled validation error messages,
    | just add a new value here and create the corresponding blade view inside "/resources/views/vendor/varbox/helpers/validation/errors".
    |
    */
    'types' => [

        'default',
        'admin',

    ],

    /*
    |
    | The default view used to render Javascript validation code.
    | This setting actually overwrites the "proengsoft/laravel-jsvalidation" -> "view" config value.
    | Because of that, publishing the actual "jsvalidation.php" config file isn't necessary anymore.
    |
    */
    'jsvalidation_view' => 'jsvalidation::bootstrap4',

];
