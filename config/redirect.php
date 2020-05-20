<?php

return [

    /*
    |
    | The redirect statuses that you will use in your application.
    |
    */
    'statuses' => [

        301 => 'Permanent (301)',
        302 => 'Normal (302)',
        307 => 'Temporary (307)',

    ],

    /*
    |
    | Export the redirects into the "bootstrap/redirects.php" on every create / update / delete.
    |
    */
    'automatic_export' => false

];
