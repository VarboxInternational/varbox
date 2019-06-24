<?php

namespace Varbox\Contracts;

interface MetaHelperContract
{
    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    public function set($key, $value);

    /**
     * @param string $key
     * @param array|string|null $default
     * @return string
     */
    public function get($key, $default = null);

    /**
     * @param string $key
     * @param array|string|null $default
     * @return string
     */
    public function tag($key, $default = null);

    /**
     * @param $keys
     * @return string
     */
    public function tags(...$keys);
}
