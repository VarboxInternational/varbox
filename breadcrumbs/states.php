<?php

/* Home > States */
Breadcrumbs::register('admin.states.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('States', route('admin.states.index'));
});

/* Home > States > Add */
Breadcrumbs::register('admin.states.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.states.index');
    $breadcrumbs->push('Add', route('admin.states.create'));
});

/* Home > States > Edit */
Breadcrumbs::register('admin.states.edit', function($breadcrumbs, $state) {
    $breadcrumbs->parent('admin.states.index');
    $breadcrumbs->push('Edit', route('admin.states.edit', $state));
});