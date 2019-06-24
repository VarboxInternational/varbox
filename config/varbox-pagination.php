<?php

return [

    /*
    |
    | These are the "pagination types" supported by the pagination helper so far (different designs/structures for the pagination functionality).
    | Each of these types have a corresponding view file inside the "/resources/views/vendor/varbox/helpers/pagination".
    | The name for each type is "/resources/views/vendor/varbox/helpers/pagination/{TYPE_FROM_BELOW}.blade.php".
    |
    | If you would like to have other pagination types, meaning additional styled pagination views,
    | just add a new value here and create the corresponding blade view inside "/resources/views/vendor/varbox/helpers/pagination".
    |
    */
    'types' => [

        'default',
        'admin',

    ],

];
