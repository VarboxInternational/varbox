<?php

return [

    /*
    |
    | All models that can implement Schema markup.
    |
    | This is used when choosing a model for which to create a Schema.
    | You should insert here the FQNs for all models that you'll want to implement Schema for.
    |
    | - key
    | The fully qualified namespace of the model class
    |
    | - value
    | Text to show in the admin, when user will choose for what to apply the created Schema
    |
    */
    'targets' => [

        \Varbox\Models\Page::class => 'All Pages'

    ],

];
