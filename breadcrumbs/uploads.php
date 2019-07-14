<?php

/* Home > Uploads */
Breadcrumbs::register('admin.uploads.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Uploads', route('admin.uploads.index'));
});