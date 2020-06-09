<?php

return [

    /*
    |
    | All email types available for your application.
    | Whenever you create a new block using the "php artisan varbox:make-block" command or manually, append the type here.
    |
    | --- [Class]:
    | The mailable class used for sending the email.
    | This gets generated automatically when running "php artisan varbox:make-mail", but you can also create it manually.
    | If your create this class manually, don't forget it will have to extend the abstract "Varbox\Mail\Mailable" class.
    |
    | --- [View]:
    | The blade file used for rendering the email.
    | The value here will be relative to the "resources/views/" directory.
    |
    | --- [Variables]:
    | Array of variables that the respective mail type is allowed to use.
    | Each array item defined here, should represent a key from the "variables" config option defined below.
    |
    */
    'types' => [

        'test-mail' => [
            'class' => 'App\Mail\TestMail',
            'view' => 'emails.test_mail',
            'variables' => [
                'username', 'home_url'
            ],
        ],

    ],

    /*
    |
    | All the available variables to be used inside mailables as dynamic content.
    | Each of these variables may belong to more than only one mail, but the implementation may differ inside each mailable class.
    |
    | --- [ARRAY KEY]:
    | The actual variable name.
    | You should reference variables by this, throughout your application.
    |
    | --- [Name]:
    | The visual name of the variable.
    |
    | --- [Label]:
    | Short description of what the variable represents.
    |
    */
    'variables' => [

        'username' => [
            'name' => 'User Name',
            'label' => 'The name of the logged in user.',
        ],

        'home_url' => [
            'name' => 'Home Url',
            'label' => 'The home URL of the site.',
        ],

    ],

];
