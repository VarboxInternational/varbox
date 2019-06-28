<?php

return [

    /*
    |
    | This option accepts an integer, representing the number of days.
    |
    | This option is used to delete notification records older than the number of days supplied when:
    | - executing the cli command: "php artisan varbox:clean-notifications"
    | - clicking the "Delete Old Notifications" button from the admin panel, inside the notifications list view
    |
    | All notifications that exceed this time will be deleted, regardless if they are already read or not.
    | If set to "null" or "0", no past notifications will be deleted whatsoever.
    |
    */
    'old_threshold' => 30,

];
