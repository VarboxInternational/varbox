<?php

/* Home > Cities */
Breadcrumbs::register('admin.cities.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Cities', route('admin.cities.index'));
});

/* Home > Cities > Add */
Breadcrumbs::register('admin.cities.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.cities.index');
    $breadcrumbs->push('Add', route('admin.cities.create'));
});

/* Home > Cities > Edit */
Breadcrumbs::register('admin.cities.edit', function($breadcrumbs, $city) {
    $breadcrumbs->parent('admin.cities.index');
    $breadcrumbs->push('Edit', route('admin.cities.edit', $city));
});