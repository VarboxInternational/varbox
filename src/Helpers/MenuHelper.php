<?php

namespace Varbox\Helpers;

use Closure;
use Illuminate\Support\Collection;
use Varbox\Contracts\MenuHelperContract;
use Varbox\Menu\MenuItem;

class MenuHelper implements MenuHelperContract
{
    /**
     * The menu items.
     *
     * @var Collection
     */
    protected $items;

    /**
     * Generate a new menu.
     *
     * @param Closure $callback
     * @return $this
     */
    public function make(Closure $callback)
    {
        $this->items = collect();

        call_user_func($callback, $this);

        return $this;
    }

    /**
     * Filter the menu items based on a callback.
     *
     * @param Closure|null $callback
     * @return $this
     */
    public function filter(Closure $callback = null)
    {
        $this->items = $this->items->filter($callback);

        return $this;
    }

    /**
     * Add a new menu item via a callback.
     * The callback should generate individual menu items.
     * Setting the properties using methods from Varbox\Menu\MenuItem
     *
     * @param Closure $callback
     */
    public function add(Closure $callback)
    {
        $item = new MenuItem;

        call_user_func($callback, $item);

        $this->items->push($item);
    }

    /**
     * Get all parent menu items.
     *
     * @return Collection
     */
    public function roots()
    {
        return $this->items->filter(function ($item) {
            return $item->parent === null;
        });
    }

    /**
     * Container for generating children menu items inside a parent node.
     * Add a new child menu item via a callback for a parent node.
     * The callback should generate individual menu items.
     * Setting the properties using methods from Varbox\Menu\MenuItem
     *
     * @param MenuItem $parent
     * @param Closure $callback
     */
    public function child(MenuItem $parent, Closure $callback)
    {
        $item = new MenuItem;
        $item->parent = $parent->id;

        call_user_func($callback, $item);

        $this->items->push($item);
    }

    /**
     * Get the children menu items corresponding to a parent.
     *
     * @param MenuItem $parent
     * @return Collection
     */
    public function children(MenuItem $parent)
    {
        return $this->items->filter(function ($item) use ($parent) {
            return $item->parent == $parent->id;
        });
    }
}
