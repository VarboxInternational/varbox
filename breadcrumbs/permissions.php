<?php

/* Home > Permissions */
Breadcrumbs::register('admin.permissions.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Permissions', route('admin.permissions.index'));
});

/* Home > Permissions > Add */
Breadcrumbs::register('admin.permissions.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.permissions.index');
    $breadcrumbs->push('Add', route('admin.permissions.create'));
});

/* Home > Permissions > Edit */
Breadcrumbs::register('admin.permissions.edit', function($breadcrumbs, $permission) {
    $breadcrumbs->parent('admin.permissions.index');
    $breadcrumbs->push('Edit', route('admin.permissions.edit', $permission));
});