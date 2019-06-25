<?php

return [

    /*
    |
    | Flag indicating whether or not activities should be logged throughout the app.
    | If set to false, no activities will be saved to the database.
    |
    */
    'enabled' => env('LOG_ACTIVITY', true),

    /*
    |
    | This option accepts an integer, representing the number of days.
    |
    | This option is used when:
    | - executing the cli command: "php artisan varbox:activity-cleanup"
    | - clicking the "cleanup" button from the admin panel, inside the activity list view
    |
    | If set to "null" or "0", no past activities will be deleted whatsoever.
    |
    */
    'delete_records_older_than' => 30,

];
