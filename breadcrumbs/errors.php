<?php

/* Home > Errors */
Breadcrumbs::register('admin.errors.index', function ($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Errors', route('admin.errors.index'));
});

/* Home > Errors > Add */
Breadcrumbs::register('admin.errors.create', function ($breadcrumbs) {
    $breadcrumbs->parent('admin.errors.index');
    $breadcrumbs->push('Add', route('admin.errors.create'));
});

/* Home > Errors > Show */
Breadcrumbs::register('admin.errors.show', function ($breadcrumbs, $error) {
    $breadcrumbs->parent('admin.errors.index');
    $breadcrumbs->push('View', route('admin.errors.show', $error));
});