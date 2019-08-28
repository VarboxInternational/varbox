<?php

use Varbox\Contracts\RevisionModelContract;

/* Home > Menu Locations */
Breadcrumbs::register('admin.menus.locations', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Locations', route('admin.menus.locations'));
});

/* Home > Menus */
Breadcrumbs::register('admin.menus.index', function($breadcrumbs, $location) {
    $breadcrumbs->parent('admin.menus.locations');
    $breadcrumbs->push('Menus', route('admin.menus.index', $location));
});

/* Home > Menus > Add */
Breadcrumbs::register('admin.menus.create', function($breadcrumbs, $location) {
    $breadcrumbs->parent('admin.menus.index', $location);
    $breadcrumbs->push('Add', route('admin.menus.create', $location));
});

/* Home > Menus > Edit */
Breadcrumbs::register('admin.menus.edit', function($breadcrumbs, $location, $menu) {
    $breadcrumbs->parent('admin.menus.index', $location);
    $breadcrumbs->push('Edit', route('admin.menus.edit', ['location' => $location, 'menu' => $menu]));
});
