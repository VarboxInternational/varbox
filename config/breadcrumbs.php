<?php

/*
|
| This is a wrapper around the "davejamesmiller/laravel-breadcrumbs" package.
| For full documentation please refer to: https://github.com/davejamesmiller/laravel-breadcrumbs
|
*/

return [

    /*
    |
    | Flag indicating whether or not breadcrumbs should be enabled in the entire admin.
    |
    */
    'enabled' => true,

    /*
    |
    | Determine whether or not to throw exceptions when some breadcrumb setup is wrong.
    |
    | An exception will be thrown in one of the following scenarios:
    | - when route-bound breadcrumbs are used but the current route doesn't have a name (UnnamedRouteException)
    | - when route-bound breadcrumbs are used and the matching breadcrumb doesn't exist (InvalidBreadcrumbException)
    | - when a named breadcrumb is used but doesn't exist (InvalidBreadcrumbException)
    |
    */
    'throw_exceptions' => true,

];
