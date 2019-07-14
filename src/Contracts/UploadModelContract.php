<?php

namespace Varbox\Contracts;

use Illuminate\Database\Schema\Blueprint;

interface UploadModelContract
{
    /**
     * @return string
     */
    public function getHelperAttribute();

    /**
     * @return string
     */
    public function getSizeMbAttribute();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $types
     */
    public function scopeOnlyTypes($query, ...$types);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $types
     */
    public function scopeExcludingTypes($query, ...$types);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $extensions
     */
    public function scopeWithExtensions($query, ...$extensions);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $extensions
     */
    public function scopeWithoutExtensions($query, ...$extensions);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $mimes
     */
    public function scopeWithMimes($query, ...$mimes);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $mimes
     */
    public function scopeWithoutMimes($query, ...$mimes);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minSize
     * @param int $maxSize
     */
    public function scopeSizeBetween($query, $minSize, $maxSize);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $attributes
     */
    public function scopeLike($query, array $attributes = []);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);

    /**
     * @return bool
     */
    public function isImage();

    /**
     * @return bool
     */
    public function isVideo();

    /**
     * @return bool
     */
    public function isAudio();

    /**
     * @return bool
     */
    public function isFile();

    /**
     * @return array
     */
    public static function getFileTypes();

    /**
     * @param string $name
     * @param \Illuminate\Database\Schema\Blueprint $table
     */
    public static function column($name, Blueprint $table);
}
