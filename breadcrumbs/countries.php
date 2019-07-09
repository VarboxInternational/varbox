<?php

/* Home > Countries */
Breadcrumbs::register('admin.countries.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Countries', route('admin.countries.index'));
});

/* Home > Countries > Add */
Breadcrumbs::register('admin.countries.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.countries.index');
    $breadcrumbs->push('Add', route('admin.countries.create'));
});

/* Home > Countries > Edit */
Breadcrumbs::register('admin.countries.edit', function($breadcrumbs, $country) {
    $breadcrumbs->parent('admin.countries.index');
    $breadcrumbs->push('Edit', route('admin.countries.edit', $country));
});