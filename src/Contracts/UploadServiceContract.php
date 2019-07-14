<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface UploadServiceContract
{
    /**
     * @param \Illuminate\Http\UploadedFile|array|string $file
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $field
     */
    public function __construct($file, Model $model = null, $field = null);

    /**
     * @return array
     */
    public static function getImageExtensions();

    /**
     * @return array
     */
    public static function getVideoExtensions();

    /**
     * @return array
     */
    public static function getAudioExtensions();

    /**
     * @return int
     */
    public static function getImageType();

    /**
     * @return int
     */
    public static function getVideoType();

    /**
     * @return int
     */
    public static function getAudioType();

    /**
     * @return int
     */
    public static function getFileType();

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
     * Verify if the file is just a regular file.
     *
     * @return bool
     */
    public function isFile();

    /**
     * @return string
     * @throws \Varbox\Exceptions\UploadException
     */
    public function upload();

    /**
     * @return void
     */
    public function unload();

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download();

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show();
}
