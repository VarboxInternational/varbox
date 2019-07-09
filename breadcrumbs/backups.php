<?php

/* Home > Backups */
Breadcrumbs::register('admin.backups.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Backups', route('admin.backups.index'));
});