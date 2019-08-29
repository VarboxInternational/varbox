<?php

/* Home > Languages */
Breadcrumbs::register('admin.languages.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Languages', route('admin.languages.index'));
});

/* Home > Languages > Add */
Breadcrumbs::register('admin.languages.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.languages.index');
    $breadcrumbs->push('Add', route('admin.languages.create'));
});

/* Home > Languages > Edit */
Breadcrumbs::register('admin.languages.edit', function($breadcrumbs, $language) {
    $breadcrumbs->parent('admin.languages.index');
    $breadcrumbs->push('Edit', route('admin.languages.edit', $language));
});
