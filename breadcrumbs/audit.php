<?php

/*
| ------------------------------------------------------------------------------------------------------------------
| Notifications
| ------------------------------------------------------------------------------------------------------------------
 */
/* Home > Notifications */
Breadcrumbs::register('admin.notifications.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Notifications', route('admin.notifications.index'));
});
/*
| ------------------------------------------------------------------------------------------------------------------
*/



/*
| ------------------------------------------------------------------------------------------------------------------
| Activity
| ------------------------------------------------------------------------------------------------------------------
*/
/* Home > Roles */
Breadcrumbs::register('admin.activity.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Activity', route('admin.activity.index'));
});
/*
| ------------------------------------------------------------------------------------------------------------------
*/
