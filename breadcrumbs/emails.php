<?php

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

/* Home > Emails > Draft */
/*Breadcrumbs::register('admin.emails.limbo', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.emails.index');
    $breadcrumbs->push('Draft', route('admin.emails.draft', $draft));
});*/

/* Home > Emails > Edit > Draft */
/*Breadcrumbs::register('admin.emails.draft', function($breadcrumbs, $draft) {
    if (!($draft instanceof \Varbox\Base\Contracts\DraftModelContract)) {
        $draft = app('draft.model')->find($draft);
    }

    $breadcrumbs->parent('admin.emails.edit', $draft->draftable);
    $breadcrumbs->push('Draft', route('admin.emails.draft', $draft));
});*/

/* Home > Emails > Edit > Revision */
/*Breadcrumbs::register('admin.emails.revision', function($breadcrumbs, $revision) {
    if (!($revision instanceof \Varbox\Base\Contracts\RevisionModelContract)) {
        $revision = app('revision.model')->find($revision);
    }

    $breadcrumbs->parent('admin.emails.edit', $revision->revisionable);
    $breadcrumbs->push('Revision', route('admin.emails.revision', $revision));
});*/