<?php

/* Home */
Breadcrumbs::register('admin', function($breadcrumbs) {
    $breadcrumbs->push('Home', route('admin'));
});