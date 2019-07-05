<?php

/*
    | ------------------------------------------------------------------------------------------------------------------
    | Configs
    | ------------------------------------------------------------------------------------------------------------------
    */
Breadcrumbs::register('admin.configs.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Configs', route('admin.configs.index'));
});

Breadcrumbs::register('admin.configs.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.configs.index');
    $breadcrumbs->push('Add', route('admin.configs.create'));
});

Breadcrumbs::register('admin.configs.edit', function($breadcrumbs, $config) {
    $breadcrumbs->parent('admin.configs.index');
    $breadcrumbs->push('Edit', route('admin.configs.edit', $config));
});
/*
| ------------------------------------------------------------------------------------------------------------------
*/



/**
| ------------------------------------------------------------------------------------------------------------------
| Errors
| ------------------------------------------------------------------------------------------------------------------
 */
Breadcrumbs::register('admin.errors.index', function ($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Errors', route('admin.errors.index'));
});

Breadcrumbs::register('admin.errors.create', function ($breadcrumbs) {
    $breadcrumbs->parent('admin.errors.index');
    $breadcrumbs->push('Add', route('admin.errors.create'));
});

Breadcrumbs::register('admin.errors.show', function ($breadcrumbs, $error) {
    $breadcrumbs->parent('admin.errors.index');
    $breadcrumbs->push('View', route('admin.errors.show', $error));
});
/**
| ------------------------------------------------------------------------------------------------------------------
 */



/**
| ------------------------------------------------------------------------------------------------------------------
| Backups
| ------------------------------------------------------------------------------------------------------------------
 */
Breadcrumbs::register('admin.backups.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Backups', route('admin.backups.index'));
});
/**
| ------------------------------------------------------------------------------------------------------------------
 */