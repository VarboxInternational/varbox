<?php

use Varbox\Contracts\RevisionModelContract;

/* Home > Blocks */
Breadcrumbs::register('admin.blocks.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Blocks', route('admin.blocks.index'));
});

/* Home > Blocks > Add */
Breadcrumbs::register('admin.blocks.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.blocks.index');
    $breadcrumbs->push('Add', route('admin.blocks.create'));
});

/* Home > Blocks > Edit */
Breadcrumbs::register('admin.blocks.edit', function($breadcrumbs, $email) {
    $breadcrumbs->parent('admin.blocks.index');
    $breadcrumbs->push('Edit', route('admin.blocks.edit', $email));
});

/* Home > Blocks > Edit > Revision */
Breadcrumbs::register('admin.blocks.revision', function($breadcrumbs, $revision) {
    if (!($revision instanceof RevisionModelContract)) {
        $revision = app(RevisionModelContract::class)->find($revision);
    }

    $breadcrumbs->parent('admin.blocks.edit', $revision->revisionable);
    $breadcrumbs->push('Revision', route('admin.blocks.revision', $revision));
});
