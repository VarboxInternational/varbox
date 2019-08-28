<?php

return [

    /*
    |
    | Here you can define different menu locations for your menu items.
    |
    | This way, in the admin you will see your new locations and you can add menu items directly to them.
    | Afterwards, on the front-end, you can use the "menu()->get('your-location')" to fetch all viable menu items from a specific location.
    |
    */
    'locations' => [

        'top', 'bottom',

    ],

    /*
    |
    | All menu types available for your application.
    |
    | --- ARRAY KEY:
    | The actual menu type name.
    | This will be persisted to the menus database table.
    |
    | --- [Class]:
    | The FQN of the model class representing the respective menu type.
    | This is used in the admin menu section, to specify a menu type upon creating / updating.
    |
    | Please note that the specified model class, will have to use the "Varbox\Base\Traits\HasUrl" trait.
    | It's actually recommended that you specify a menu type for each model class of yours that uses the "HasUrl" trait.
    | That way, you will be able to reference entity records, directly in your menus from the admin, thus displaying them on front-end.
    |
    */
    'types' => [

        'url' => [
            'class' => null,
        ],

        'page' => [
            'class' => \Varbox\Models\Page::class,
        ],

    ],

];
