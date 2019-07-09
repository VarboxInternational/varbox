<?php

/* Home > Configs */
Breadcrumbs::register('admin.configs.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Configs', route('admin.configs.index'));
});

/* Home > Configs > Add */
Breadcrumbs::register('admin.configs.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.configs.index');
    $breadcrumbs->push('Add', route('admin.configs.create'));
});

/* Home > Configs > Edit */
Breadcrumbs::register('admin.configs.edit', function($breadcrumbs, $config) {
    $breadcrumbs->parent('admin.configs.index');
    $breadcrumbs->push('Edit', route('admin.configs.edit', $config));
});