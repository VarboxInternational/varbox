<?php

use Varbox\Contracts\RevisionModelContract;

/* Home > Schema */
Breadcrumbs::register('admin.schema.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Schema', route('admin.schema.index'));
});

/* Home > Schema > Add */
Breadcrumbs::register('admin.schema.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.schema.index');
    $breadcrumbs->push('Add', route('admin.schema.create'));
});

/* Home > Schema > Edit */
Breadcrumbs::register('admin.schema.edit', function($breadcrumbs, $block) {
    $breadcrumbs->parent('admin.schema.index');
    $breadcrumbs->push('Edit', route('admin.schema.edit', $block));
});
