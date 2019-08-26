<?php

return [

    /*
    |
    | How many records to display per page.
    |
    */
    'per_page' => 30,

    /*
    |
    | Flag whether or not to wrap every database operation inside a big transaction.
    |
    | If set to true, each of these methods will be contained inside their own transaction:
    | - store, update, destroy
    |
    | This only applies if you use the underlying methods of the "CanCrud" trait.
    | - _store(), _update(), _destroy()
    |
    | To customize this at entity level, you can implement the "shouldUseTransactions()" method inside your controllers.
    |
    */
    'use_transactions' => true,

    /*
    |
    | The list of exceptions that are treated softly (not throwable).
    |
    | When an exception defined here is caught by the CRUD functionality.
    | Instead of throwing it, the exception will be softly handled.
    |
    | This means that either an error message will appear or a redirect will happen.
    |
    */
    'soft_exceptions' => [
        \Varbox\Exceptions\CrudException::class,
    ],

];
