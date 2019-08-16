<?php

use Varbox\Contracts\RevisionModelContract;

/* Home > Pages */
Breadcrumbs::register('admin.pages.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Pages', route('admin.pages.index'));
});

/* Home > Pages > Add */
Breadcrumbs::register('admin.pages.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.pages.index');
    $breadcrumbs->push('Add', route('admin.pages.create'));
});

/* Home > Pages > Edit */
Breadcrumbs::register('admin.pages.edit', function($breadcrumbs, $page) {
    $breadcrumbs->parent('admin.pages.index');
    $breadcrumbs->push('Edit', route('admin.pages.edit', $page));
});

/* Home > Pages > Edit > Revision */
Breadcrumbs::register('admin.pages.revision', function($breadcrumbs, $revision) {
    if (!($revision instanceof RevisionModelContract)) {
        $revision = app(RevisionModelContract::class)->find($revision);
    }

    $breadcrumbs->parent('admin.pages.edit', $revision->revisionable);
    $breadcrumbs->push('Revision', route('admin.pages.revision', $revision));
});
