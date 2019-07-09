<?php

/* Home > User > Addresses */
Breadcrumbs::register('admin.addresses.index', function($breadcrumbs, $user) {
    $breadcrumbs->parent('admin.users.edit', $user);
    $breadcrumbs->push('Addresses', route('admin.addresses.index', [$user->getKey()]));
});

/* Home > User > Addresses > Add */
Breadcrumbs::register('admin.addresses.create', function($breadcrumbs, $user) {
    $breadcrumbs->parent('admin.addresses.index', $user);
    $breadcrumbs->push('Add', route('admin.addresses.create', $user->getKey()));
});

/* Home > User > Addresses > Edit */
Breadcrumbs::register('admin.addresses.edit', function($breadcrumbs, $user, $address) {
    $breadcrumbs->parent('admin.addresses.index', $user);
    $breadcrumbs->push('Edit', route('admin.addresses.edit', ['user' => $user->getKey(), 'address' => $address->getKey()]));
});