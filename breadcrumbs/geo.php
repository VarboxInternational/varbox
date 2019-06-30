<?php

/*
| ------------------------------------------------------------------------------------------------------------------
| Countries
| ------------------------------------------------------------------------------------------------------------------
*/
/* Home > Countries */
Breadcrumbs::register('admin.countries.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Countries', route('admin.countries.index'));
});

/* Home > Countries > Add */
Breadcrumbs::register('admin.countries.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.countries.index');
    $breadcrumbs->push('Add', route('admin.countries.create'));
});

/* Home > Countries > Edit */
Breadcrumbs::register('admin.countries.edit', function($breadcrumbs, $country) {
    $breadcrumbs->parent('admin.countries.index');
    $breadcrumbs->push('Edit', route('admin.countries.edit', $country));
});
/*
| ------------------------------------------------------------------------------------------------------------------
*/



/*
| ------------------------------------------------------------------------------------------------------------------
| States
| ------------------------------------------------------------------------------------------------------------------
*/
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
/*
| ------------------------------------------------------------------------------------------------------------------
*/



/*
| ------------------------------------------------------------------------------------------------------------------
| Cities
| ------------------------------------------------------------------------------------------------------------------
*/
/* Home > Cities */
Breadcrumbs::register('admin.cities.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Cities', route('admin.cities.index'));
});

/* Home > Cities > Add */
Breadcrumbs::register('admin.cities.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.cities.index');
    $breadcrumbs->push('Add', route('admin.cities.create'));
});

/* Home > Cities > Edit */
Breadcrumbs::register('admin.cities.edit', function($breadcrumbs, $city) {
    $breadcrumbs->parent('admin.cities.index');
    $breadcrumbs->push('Edit', route('admin.cities.edit', $city));
});
/*
| ------------------------------------------------------------------------------------------------------------------
*/



/*
| ------------------------------------------------------------------------------------------------------------------
| Addresses
| ------------------------------------------------------------------------------------------------------------------
*/
/* Home > Addresses */
Breadcrumbs::register('admin.addresses.index', function($breadcrumbs, $user) {
    $breadcrumbs->parent('admin.users.edit', $user);
    $breadcrumbs->push('Addresses', route('admin.addresses.index', [$user->getKey()]));
});

/* Home > Addresses > Add */
Breadcrumbs::register('admin.addresses.create', function($breadcrumbs, $user) {
    $breadcrumbs->parent('admin.addresses.index', $user);
    $breadcrumbs->push('Add', route('admin.addresses.create', $user->getKey()));
});

/* Home > Addresses > Edit */
Breadcrumbs::register('admin.addresses.edit', function($breadcrumbs, $user, $address) {
    $breadcrumbs->parent('admin.addresses.index', $user);
    $breadcrumbs->push('Edit', route('admin.addresses.edit', ['user' => $user->getKey(), 'address' => $address->getKey()]));
});
/*
| ------------------------------------------------------------------------------------------------------------------
*/