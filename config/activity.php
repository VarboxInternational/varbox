<?php

return [

    /*
    |
    | Flag indicating whether or not activities should be logged throughout the app.
    | If set to false, no activities will be saved to the database.
    |
    */
    'enabled' => env('LOG_ACTIVITY', false),

    /*
    |
    | This option accepts an integer, representing the number of days.
    |
    | This option is used to delete activity records older than the number of days supplied when:
    | - executing the cli command: "php artisan varbox:clean-activity"
    | - clicking the "Delete Old Activity" button from the admin panel, inside the activity list view
    |
    | If set to "null" or "0", no past activities will be deleted whatsoever.
    |
    */
    'old_threshold' => 30,

];
