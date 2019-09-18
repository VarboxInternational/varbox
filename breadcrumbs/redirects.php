<?php

/* Home > Redirects */
Breadcrumbs::register('admin.redirects.index', function ($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Redirects', route('admin.redirects.index'));
});

/* Home > Redirects > Add */
Breadcrumbs::register('admin.redirects.create', function ($breadcrumbs) {
    $breadcrumbs->parent('admin.redirects.index');
    $breadcrumbs->push('Add', route('admin.redirects.create'));
});

/* Home > Redirects > Edit */
Breadcrumbs::register('admin.redirects.edit', function ($breadcrumbs, $redirect) {
    $breadcrumbs->parent('admin.redirects.index');
    $breadcrumbs->push('Edit', route('admin.redirects.edit', $redirect));
});
