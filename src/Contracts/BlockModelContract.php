<?php

namespace Varbox\Contracts;

interface BlockModelContract
{
    /**
     * @param string $class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function blockables($class);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);

    /**
     * @return array
     */
    public static function getTypes();

    /**
     * @return array
     */
    public static function getLocations();

    /**
     * @return array
     */
    public static function getTypesForSelect();

    /**
     * @return array
     */
    public static function getClassesForSelect();

    /**
     * @return array
     */
    public static function getPathsForSelect();

    /**
     * @return array
     */
    public static function getImagesForSelect();
}
