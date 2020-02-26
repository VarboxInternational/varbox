<?php

/* Home > Admins */
Breadcrumbs::register('admin.admins.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Admins', route('admin.admins.index'));
});

/* Home > Admins > Add */
Breadcrumbs::register('admin.admins.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.admins.index');
    $breadcrumbs->push('Add', route('admin.admins.create'));
});

/* Home > Admins > Edit */
Breadcrumbs::register('admin.admins.edit', function($breadcrumbs, $user) {
    $breadcrumbs->parent('admin.admins.index');
    $breadcrumbs->push('Edit', route('admin.admins.edit', $user->getKey()));
});
