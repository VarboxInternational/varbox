<?php

namespace Varbox;

use Exception;

class Varbox
{
    /**
     * Require all routes.
     *
     * @return void
     * @throws Exception
     */
    public function route()
    {
        if (file_exists(__DIR__ . '/../routes/last.php')) {
            require __DIR__ . '/../routes/last.php';
        }
    }
}
