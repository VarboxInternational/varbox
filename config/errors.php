<?php

return [

    /*
    |
    | Flag indicating whether or not errors should be logged throughout the app.
    | If set to false, no errors will be saved to the database.
    |
    */
    'enabled' => env('SAVE_ERRORS', true),

    /*
    |
    | This option accepts an integer, representing the number of days.
    |
    | This option is used to delete error records older than the number of days supplied when:
    | - executing the cli command: "php artisan varbox:clean-errors"
    | - clicking the "Delete Old Errors" button from the admin panel, inside the error list view
    |
    | If set to "null" or "0", no past errors will be deleted whatsoever.
    |
    */
    'old_threshold' => 30,

    /*
    |
    | You can be notified via email when an error has occurred in the system.
    | To do so, specify any email addresses in an array format and each address will receive the notifications.
    |
    */
    'notification_emails' => [

    ]

];
