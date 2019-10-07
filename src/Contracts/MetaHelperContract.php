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
     * @return string
     */
    public function get($key);

    /**
     * @param string $key
     * @return string
     */
    public function tag($key);

    /**
     * @param $keys
     * @return string
     */
    public function tags(...$keys);
}
