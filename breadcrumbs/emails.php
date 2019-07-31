<?php

use Varbox\Contracts\RevisionModelContract;

/* Home > Emails */
Breadcrumbs::register('admin.emails.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Emails', route('admin.emails.index'));
});

/* Home > Emails > Add */
Breadcrumbs::register('admin.emails.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.emails.index');
    $breadcrumbs->push('Add', route('admin.emails.create'));
});

/* Home > Emails > Edit */
Breadcrumbs::register('admin.emails.edit', function($breadcrumbs, $email) {
    $breadcrumbs->parent('admin.emails.index');
    $breadcrumbs->push('Edit', route('admin.emails.edit', $email));
});

/* Home > Emails > Edit > Revision */
Breadcrumbs::register('admin.emails.revision', function($breadcrumbs, $revision) {
    if (!($revision instanceof RevisionModelContract)) {
        $revision = app(RevisionModelContract::class)->find($revision);
    }

    $breadcrumbs->parent('admin.emails.edit', $revision->revisionable);
    $breadcrumbs->push('Revision', route('admin.emails.revision', $revision));
});