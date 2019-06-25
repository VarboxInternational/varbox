<?php

namespace Varbox;

use Exception;

class Varbox
{
    /**
     * All of the available VarBox modules that can be installed so far.
     *
     * @var array
     */
    protected $availableModules = [
        'cms', 'shop', 'geo', 'trans', 'seo', 'media', 'audit', 'sys',
    ];

    /**
     * Verify if a given module is enabled.
     *
     * @param string $module
     * @return bool
     * @throws Exception
     */
    public function moduleEnabled($module)
    {
        $this->verifyModule($module);

        return config('varbox.varbox-modules.' . $module) === true;
    }

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

    /**
     * Verify if the given module is part of the suite.
     *
     * @param string $module
     * @throws Exception
     */
    private function verifyModule($module)
    {
        if (!in_array(strtolower($module), array_keys($this->availableModules))) {
            throw new Exception(
                'The module "' . $module . '" does not exist!' . PHP_EOL .
                'The available modules are:' . PHP_EOL .
                implode(', ', array_keys($this->availableModules))
            );
        }
    }
}
