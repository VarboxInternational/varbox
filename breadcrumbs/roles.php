<?php

/* Home > Roles */
Breadcrumbs::register('admin.roles.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Roles', route('admin.roles.index'));
});

/* Home > Roles > Add */
Breadcrumbs::register('admin.roles.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.roles.index');
    $breadcrumbs->push('Add', route('admin.roles.create'));
});

/* Home > Roles > Edit */
Breadcrumbs::register('admin.roles.edit', function($breadcrumbs, $role) {
    $breadcrumbs->parent('admin.roles.index');
    $breadcrumbs->push('Edit', route('admin.roles.edit', $role));
});