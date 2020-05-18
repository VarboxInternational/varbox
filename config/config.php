<?php

return [

    /*
    |
    | Flag indicating whether or not configuration values can be modified from admin on runtime.
    |
    */
    'enabled' => env('OVERWRITE_CONFIGS', false),

    /*
    |
    | The list of Laravel configurations that can be editable from the admin.
    |
    | Specify any config key from any Laravel configuration file to make it editable.
    | The format used to specify the config keys is the same as using Laravel's "config()" helper.
    |
    | From Admin -> System Settings -> Configs you will be able to only create/update config vaues for the keys specified here.
    |
    | Examples:
    | - specify "app.name" as part of the below array to make the "name" key from the "app.php" config file editable;
    | - specify "auth.defaults.guard" as part of the below array to make the "defaults -> guard" key from the "auth.php" config file editable;
    |
    | Note: for config keys supporting arrays, simply define your values in Admin, separated by a semi-colon (;).
    |
    */
    'keys' => [

    ],

];
