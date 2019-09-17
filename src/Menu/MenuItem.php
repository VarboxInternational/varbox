<?php

namespace Varbox\Menu;

use Illuminate\Support\Str;

class MenuItem
{
    /**
     * The id of the menu item.
     *
     * @var
     */
    public $id;

    /**
     * The parent item for a menu item.
     *
     * @var
     */
    public $parent;

    /**
     * The name of a menu item.
     *
     * @var
     */
    public $name;

    /**
     * The url of a menu item.
     *
     * @var
     */
    public $url;

    /**
     * The active identifier for a menu item.
     *
     * @var
     */
    public $active = [];

    /**
     * The permissions that a menu item requires met for it to dislpay.
     *
     * @var array
     */
    public $permissions = [];

    /**
     * Container for additional menu item properties.
     *
     * @var array
     */
    public $data = [];

    /**
     * Set an id for the current menu item.
     *
     * @set $id
     */
    public function __construct()
    {
        $this->id = uniqid(rand(), true);
    }

    /**
     * Set|Get the name property for the current menu item.
     *
     * @param string|null $name
     * @return $this|string
     */
    public function name($name = null)
    {
        if ($name === null) {
            return $this->name;
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Set|Get the url property for the current menu item.
     *
     * @param string|null $url
     * @return $this|string
     */
    public function url($url = null)
    {
        if ($url === null) {
            return $this->url;
        }

        $this->url = $url;

        return $this;
    }

    /**
     * Set|Get the active property for the current menu item.
     *
     * @param array ...$active
     * @return $this|bool
     */
    public function active(...$active)
    {
        if (!$active) {
            foreach ($this->active as $active) {
                if (
                    (Str::contains($active, '*') && Str::startsWith(request()->path(), trim($active, '*/'))) ||
                    request()->path() == $active
                ) {
                    return true;
                    break;
                }
            }

            return false;
        }

        $this->active = $active;

        return $this;
    }

    /**
     * Set|Get the permissions property for the current menu item.
     *
     * @param array|null $permissions
     * @return $this|array
     */
    public function permissions(...$permissions)
    {
        if (!$permissions) {
            return $this->permissions;
        }

        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Set|Get the data property for the current menu item.
     *
     * @param string|null $key
     * @param string|null $value
     * @return $this|array|string
     */
    public function data($key = null, $value = null)
    {
        if ($value === null) {
            return $key ? $this->data[$key] : $this->data;
        }

        $this->data[$key] = $value;

        return $this;
    }
}
