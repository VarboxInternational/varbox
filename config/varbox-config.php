<?php

return [

    /*
    |
    | The list of Laravel configurations that can be editable from the admin.
    |
    | Specify any config key from any Laravel configuration file to make it editable.
    | The format used to specify the config keys is the same as using Laravel's "config()" helper.
    |
    | Ones specified, you will find the config key inside "Admin -> System Settings -> Configs", ready to be edited.
    | Please note that if a config key is left empty in the Admin, the actual value of that config key will be what's specified in the corresponding Laravel config file, and NOT "null".
    |
    | Examples:
    | - specify "app.name" as part of the below array to make the "name" key from the "app.php" config file editable;
    | - specify "auth.defaults.guard" as part of the below array to make the "defaults -> guard" key from the "auth.php" config file editable;
    |
    | Note: for config keys supporting arrays, simply define your values in Admin, separated by a semi-colon (;).
    |
    | Leave the array empty to have no config keys editable.
    |
    */
    'name' => [

    ],

];
