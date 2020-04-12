<?php

namespace Varbox\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Varbox\Contracts\UploadedHelperContract;
use Varbox\Contracts\UploadServiceContract;
use Varbox\Exceptions\UploadException;

trait HasUploads
{
    /**
     * This method should be called inside the models using this trait.
     * In this method you can overwrite any config value set in config/upload.php
     * Just return an array like in config/varbox/upload.php, specifying only the keys you wish to overwrite.
     *
     * @return array
     */
    abstract function getUploadConfig(): array;

    /**
     * Manage uploading of files.
     *
     * The uploading will be done based on the file type: image|video|audio|file.
     * Every uploading type has custom logic applicable only for that type.
     *
     * If saving to database is enabled in config/varbox/upload.php, that will be done too.
     * If forgetting old uploads is enabled in config/varbox/upload.php, that will be done too.
     *
     * If anything fails with the uploading process, restore everything and throw a specific error.
     *
     * @param UploadedFile|array|string $file
     * @param string $field
     * @return string
     * @throws UploadException
     */
    public function uploadFile($file, $field)
    {
        return app(UploadServiceContract::class, [
            'file' => $file,
            'model' => $this,
            'field' => $field,
        ])->upload();
    }

    /**
     * Download an existing file by it's full path.
     *
     * @param string $field
     * @return string
     */
    public function downloadFile($field)
    {
        return app(UploadServiceContract::class, [
            'file' => $this->normalizeFileField($field),
        ])->download();
    }

    /**
     * Display an existing file by it's full path.
     *
     * @param string $field
     * @return string
     */
    public function showFile($field)
    {
        return app(UploadServiceContract::class, [
            'file' => $this->normalizeFileField($field),
        ])->show();
    }

    /**
     * Get the "uploaded" helper instance.
     * To this, uploaded specific methods can be chained to obtain the file's source url.
     *
     * @param string $field
     * @return UploadedHelperContract
     */
    public function uploadedFile($field)
    {
        return uploaded($this->normalizeFileField($field));
    }

    /**
     * Get the actual value of the field for a loaded model.
     * Check for metadata usage also.
     *
     * @param string $field
     * @return mixed
     */
    protected function normalizeFileField($field)
    {
        if (Str::contains($field, '[') && Str::contains($field, ']')) {
            $attribute = strtok($field, '[');

            return Arr::get(
                get_object_vars_recursive($this->{$attribute}),
                str_replace('][', '.', trim(str_replace($attribute, '', $field), '.[]'))
            );
        }

        return $this->{$field};
    }
}
