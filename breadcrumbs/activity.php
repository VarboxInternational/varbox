<?php

/* Home > Activity */
Breadcrumbs::register('admin.activity.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Activity', route('admin.activity.index'));
});