<?php

/* Home > Translations */
Breadcrumbs::register('admin.translations.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Translations    ', route('admin.translations.index'));
});

/* Home > Translations > Edit */
Breadcrumbs::register('admin.translations.edit', function($breadcrumbs, $country) {
    $breadcrumbs->parent('admin.translations.index');
    $breadcrumbs->push('Edit', route('admin.translations.edit', $country));
});
