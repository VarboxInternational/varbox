<?php

/* Home > Users */
Breadcrumbs::register('admin.users.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Users', route('admin.users.index'));
});

/* Home > Users > Add */
Breadcrumbs::register('admin.users.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.users.index');
    $breadcrumbs->push('Add', route('admin.users.create'));
});

/* Home > Users > Edit */
Breadcrumbs::register('admin.users.edit', function($breadcrumbs, $user) {
    $breadcrumbs->parent('admin.users.index');
    $breadcrumbs->push('Edit', route('admin.users.edit', $user->getKey()));
});