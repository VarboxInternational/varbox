<?php

/* Home > Notifications */
Breadcrumbs::register('admin.notifications.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Notifications', route('admin.notifications.index'));
});