<?php

namespace Varbox\Helpers;

use Illuminate\Support\Facades\Storage;
use Varbox\Contracts\UploadedHelperContract;
use Varbox\Contracts\UploadModelContract;
use Varbox\Services\UploadService;

class UploadedHelper implements UploadedHelperContract
{
    /**
     * The full path to the original file, without any style appended to it.
     *
     * @var string
     */
    protected $original;

    /**
     * The full path to the file.
     *
     * @var string
     */
    protected $file;
    /**
     * The filesystem disk used to search the files in.
     *
     * @var string
     */
    protected $disk;

    /**
     * The extension of the provided file.
     *
     * @var string
     */
    protected $extension;

    /**
     * The type of the file.
     * TYPE_NORMAL | TYPE_IMAGE | TYPE_VIDEO
     *
     * @var string
     */
    protected $type;

    /**
     * The types a file can have.
     * This will be used by this helper to resolve methods specifically by file type.
     *
     * @const
     */
    const TYPE_NORMAL = 1;
    const TYPE_IMAGE = 2;
    const TYPE_VIDEO = 3;

    /**
     * Build a fully configured uploaded helper instance.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->setOriginal($file)->setFile($file)->setDisk()->setExtension()->setType();
    }

    /**
     * Set the original property to know the original path along the way.
     *
     * @param string $file
     * @return $this
     */
    public function setOriginal($file)
    {
        $this->original = $file;

        return $this;
    }

    /**
     * Get the original file.
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Set the file to work with.
     *
     * @param string $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the file path.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the storage disk to work with.
     *
     * @return $this
     */
    public function setDisk()
    {
        $this->disk = config('varbox.upload.storage.disk', 'uploads');

        return $this;
    }

    /**
     * Get the storage disk.
     *
     * @return string
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * Set the file extension.
     *
     * @return $this
     */
    public function setExtension()
    {
        $this->extension = strtolower(last(explode('.', $this->file)));

        return $this;
    }

    /**
     * Get the file extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the file type.
     *
     * @return $this
     */
    public function setType()
    {
        $uploadService = config('varbox.bindings.services.upload_service', UploadService::class);

        switch ($this->extension) {
            case in_array($this->extension, $uploadService::getImageExtensions()):
                $this->type = self::TYPE_IMAGE;
                break;
            case in_array($this->extension, $uploadService::getVideoExtensions()):
                $this->type = self::TYPE_VIDEO;
                break;
            default:
                $this->type = self::TYPE_NORMAL;
                break;
        }

        return $this;
    }

    /**
     * Get the file type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the $file to the exact path of the provided video's thumbnail.
     * The $number parameter is used to specify which video thumbnail to identify: 1st, 2nd, 3rd, etc.
     * Keep in mind that this method will only have an effect on video type files.
     *
     * @param int|null $number
     * @return string
     */
    public function thumbnail($number = null)
    {
        $this->file = $this->original;

        if ($this->type == self::TYPE_IMAGE) {
            $this->file = substr_replace(
                preg_replace('/\..+$/', '.' . $this->extension, $this->file), '_thumbnail', strpos($this->file, '.'), 0
            );
        }

        if ($this->type == self::TYPE_VIDEO) {
            $this->file = substr_replace(
                preg_replace('/\..+$/', '.jpg', $this->file), '_thumbnail_' . ($number ?: 1), strpos($this->file, '.'), 0
            );
        }

        return Storage::disk($this->disk)->url($this->file);
    }

    /**
     * Get the parsed file's full url.
     * You can specify which style instance of the file you want to get.
     * However, specifying the style is taking into consideration only if the file is an actual image or video.
     * If "original" is specified as the style, it will just return the original file.
     *
     * @param string|null $style
     * @return string
     */
    public function url($style = null)
    {
        $this->file = $this->original;

        if ($style && $style != 'original' && ($this->type == self::TYPE_IMAGE || $this->type == self::TYPE_VIDEO)) {
            $this->file = substr_replace(
                $this->file, '_' . $style, strpos($this->file, '.'), 0
            );
        }

        return Storage::disk($this->disk)->url($this->file);
    }

    /**
     * Get the partial or full path of the file.
     * You can specify which style instance of the file you want to get.
     * However, specifying the style is taking into consideration only if the file is an actual image or video.
     * If "original" is specified as the style, it will just return the original file.
     *
     * @param string|null $style
     * @param bool $full
     * @return string
     */
    public function path($style = null, $full = false)
    {
        $this->file = $this->original;

        if ($style && $style != 'original' && ($this->type == self::TYPE_IMAGE || $this->type == self::TYPE_VIDEO)) {
            $this->file = substr_replace(
                $this->file, '_' . $style, strpos($this->file, '.'), 0
            );
        }

        return $full === true ?
            Storage::disk($this->disk)->getDriver()->getAdapter()->applyPathPrefix($this->file) :
            $this->file;
    }

    /**
     * Get the Upload instance representing the field's upload.
     *
     * @return UploadModelContract|null
     */
    public function load()
    {
        return config('varbox.upload.database.save', true) ?
            app('upload.model')->whereFullPath($this->file)->first() : null;
    }

    /**
     * Check if the given file exists in storage.
     *
     * @return bool
     */
    public function exists()
    {
        return Storage::disk($this->disk)->exists($this->file);
    }
}
