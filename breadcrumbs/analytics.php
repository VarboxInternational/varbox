<?php

/* Home > Analytics */
Breadcrumbs::register('admin.analytics.show', function ($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Analytics', route('admin.analytics.show'));
});
