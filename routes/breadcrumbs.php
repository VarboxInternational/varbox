<?php

if (config('varbox.varbox-breadcrumb.enable', true) === true) {
    /*
    | ------------------------------------------------------------------------------------------------------------------
    | Dashboard
    | ------------------------------------------------------------------------------------------------------------------
    */
    /* Home */
    Breadcrumbs::register('admin', function($breadcrumbs) {
        $breadcrumbs->push('Home', route('admin'));
    });
    /*
    | ------------------------------------------------------------------------------------------------------------------
    */



    /*
    | ------------------------------------------------------------------------------------------------------------------
    | Users
    | ------------------------------------------------------------------------------------------------------------------
    */
    /* Home > Users */
    Breadcrumbs::register('admin.users.index', function($breadcrumbs) {
        $breadcrumbs->parent('admin');
        $breadcrumbs->push('Users', route('admin.users.index'));
    });

    /* Home > Users > Add */
    Breadcrumbs::register('admin.users.create', function($breadcrumbs) {
        $breadcrumbs->parent('admin.users.index');
        $breadcrumbs->push('Add', route('admin.users.create'));
    });

    /* Home > Users > Edit */
    Breadcrumbs::register('admin.users.edit', function($breadcrumbs, $user) {
        $breadcrumbs->parent('admin.users.index');
        $breadcrumbs->push('Edit', route('admin.users.edit', $user));
    });
    /*
    | ------------------------------------------------------------------------------------------------------------------
    */



    /*
    | ------------------------------------------------------------------------------------------------------------------
    | Admins
    | ------------------------------------------------------------------------------------------------------------------
     */
    /* Home > Admins */
    Breadcrumbs::register('admin.admins.index', function($breadcrumbs) {
        $breadcrumbs->parent('admin');
        $breadcrumbs->push('Admins', route('admin.admins.index'));
    });

    /* Home > Admins > Add */
    Breadcrumbs::register('admin.admins.create', function($breadcrumbs) {
        $breadcrumbs->parent('admin.admins.index');
        $breadcrumbs->push('Add', route('admin.admins.create'));
    });

    /* Home > Admins > Edit */
    Breadcrumbs::register('admin.admins.edit', function($breadcrumbs, $admin) {
        $breadcrumbs->parent('admin.admins.index');
        $breadcrumbs->push('Edit', route('admin.admins.edit', $admin));
    });
    /*
    | ------------------------------------------------------------------------------------------------------------------
    */



    /*
    | ------------------------------------------------------------------------------------------------------------------
    | Roles
    | ------------------------------------------------------------------------------------------------------------------
    */
    /* Home > Roles */
    Breadcrumbs::register('admin.roles.index', function($breadcrumbs) {
        $breadcrumbs->parent('admin');
        $breadcrumbs->push('Roles', route('admin.roles.index'));
    });

    /* Home > Roles > Add */
    Breadcrumbs::register('admin.roles.create', function($breadcrumbs) {
        $breadcrumbs->parent('admin.roles.index');
        $breadcrumbs->push('Add', route('admin.roles.create'));
    });

    /* Home > Roles > Edit */
    Breadcrumbs::register('admin.roles.edit', function($breadcrumbs, $role) {
        $breadcrumbs->parent('admin.roles.index');
        $breadcrumbs->push('Edit', route('admin.roles.edit', $role));
    });
    /*
    | ------------------------------------------------------------------------------------------------------------------
    */



    /*
    | ------------------------------------------------------------------------------------------------------------------
    | Permissions
    | ------------------------------------------------------------------------------------------------------------------
     */
    /* Home > Permissions */
    Breadcrumbs::register('admin.permissions.index', function($breadcrumbs) {
        $breadcrumbs->parent('admin');
        $breadcrumbs->push('Permissions', route('admin.permissions.index'));
    });

    /* Home > Permissions > Add */
    Breadcrumbs::register('admin.permissions.create', function($breadcrumbs) {
        $breadcrumbs->parent('admin.permissions.index');
        $breadcrumbs->push('Add', route('admin.permissions.create'));
    });

    /* Home > Permissions > Edit */
    Breadcrumbs::register('admin.permissions.edit', function($breadcrumbs, $permission) {
        $breadcrumbs->parent('admin.permissions.index');
        $breadcrumbs->push('Edit', route('admin.permissions.edit', $permission));
    });
    /*
    | ------------------------------------------------------------------------------------------------------------------
    */
}
