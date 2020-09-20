<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Varbox\Contracts\UploadModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Services\UploadService;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsCsvExportable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Upload extends Model implements UploadModelContract
{
    use HasFactory;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use IsCsvExportable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'uploads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'original_name',
        'path',
        'full_path',
        'extension',
        'size',
        'mime',
        'type',
    ];

    /**
     * The file types available: image, video, audio, file
     *
     * @var array
     */
    protected static $types;

    /**
     * Get the size in megabytes.
     *
     * @return string
     */
    public function getHelperAttribute()
    {
        return uploaded($this->attributes['full_path']);
    }

    /**
     * Get the size in megabytes.
     *
     * @return string
     */
    public function getSizeMbAttribute()
    {
        return number_format($this->attributes['size'] / pow(1024, 2), 2);
    }

    /**
     * Filter query results to show uploads only of type.
     * Param $types: single upload type as string or multiple upload types as an array.
     *
     * @param Builder $query
     * @param $types
     */
    public function scopeOnlyTypes($query, ...$types)
    {
        $types = Arr::flatten($types);

        if (!empty($types)) {
            $query->where(function ($q) use ($types) {
                foreach ($types as $type) {
                    if ($type) {
                        $q->orWhere('type', is_numeric($type) ?
                            $type : array_search(Str::title($type), static::getFileTypes())
                        );
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads excluding the given types.
     * Param $types: single upload type as string or multiple upload types as an array.
     *
     * @param Builder $query
     * @param $types
     */
    public function scopeExcludingTypes($query, ...$types)
    {
        $types = Arr::flatten($types);

        if (!empty($types)) {
            $query->where(function ($q) use ($types) {
                foreach ($types as $type) {
                    if ($type) {
                        $q->where('type', '!=', is_numeric($type) ? $type :
                            array_search(Str::title($type), static::getFileTypes())
                        );
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads only of the given extensions.
     * Param $extensions: single upload extension as string or multiple upload extensions as an array.
     *
     * @param Builder $query
     * @param $extensions
     */
    public function scopeWithExtensions($query, ...$extensions)
    {
        $extensions = Arr::flatten($extensions);

        if (!empty($extensions)) {
            $query->where(function ($q) use ($extensions) {
                foreach ($extensions as $extension) {
                    if ($extension) {
                        $q->orWhere('extension', strtolower($extension));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads excluding the given extensions.
     * Param $extensions: single upload extension as string or multiple upload extensions as an array.
     *
     * @param Builder $query
     * @param $extensions
     */
    public function scopeWithoutExtensions($query, ...$extensions)
    {
        $extensions = Arr::flatten($extensions);

        if (!empty($extensions)) {
            $query->where(function ($q) use ($extensions) {
                foreach ($extensions as $extension) {
                    if ($extension) {
                        $q->where('extension', '!=', strtolower($extension));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads only of the given mime types.
     * Param $mimes: single upload mime as string or multiple upload mimes as an array.
     *
     * @param Builder $query
     * @param $mimes
     */
    public function scopeWithMimes($query, ...$mimes)
    {
        $mimes = Arr::flatten($mimes);

        if (!empty($mimes)) {
            $query->where(function ($q) use ($mimes) {
                foreach ($mimes as $mime) {
                    if ($mime) {
                        $q->orWhere('mime', strtolower($mime));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads excluding the given mimes.
     * Param $mimes: single upload mime as string or multiple upload mimes as an array.
     *
     * @param Builder $query
     * @param $mimes
     */
    public function scopeWithoutMimes($query, ...$mimes)
    {
        $mimes = Arr::flatten($mimes);

        if (!empty($mimes)) {
            $query->where(function ($q) use ($mimes) {
                foreach ($mimes as $mime) {
                    if ($mime) {
                        $q->where('mime', '!=', strtolower($mime));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads only between the given sizes.
     * Param $minSize: the minimum size in MB.
     * Param $maxSize: the maximum size in MB.
     *
     * @param Builder $query
     * @param int $minSize
     * @param int $maxSize
     */
    public function scopeSizeBetween($query, $minSize, $maxSize)
    {
        $query->whereBetween('size', [
            $minSize * pow(1024, 2),
            $maxSize * pow(1024, 2)
        ]);
    }

    /**
     * Filter query results to show uploads that match the search criteria.
     * Param $attributes: an array containing field => value.
     *
     * @param Builder $query
     * @param array $attributes
     */
    public function scopeLike($query, array $attributes = [])
    {
        if (!empty($attributes)) {
            $query->where(function ($q) use ($attributes) {
                foreach ($attributes as $field => $value) {
                    if ($value) {
                        $q->orWhere($field, 'like', '%' . $value . '%');
                    }
                }
            });
        }
    }

    /**
     * Sort the query alphabetically by original_name.
     *
     * @param $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('original_name', 'asc');
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        $upload = config('varbox.bindings.services.upload_service', UploadService::class);

        return $this->type == $upload::getImageType();
    }

    /**
     * @return bool
     */
    public function isVideo()
    {
        $upload = config('varbox.bindings.services.upload_service', UploadService::class);

        return $this->type == $upload::getVideoType();
    }

    /**
     * @return bool
     */
    public function isAudio()
    {
        $upload = config('varbox.bindings.services.upload_service', UploadService::class);

        return $this->type == $upload::getAudioType();
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return !$this->isImage() && !$this->isVideo() && !$this->isAudio();
    }

    /**
     * Get all available file types.
     *
     * @return array
     */
    public static function getFileTypes()
    {
        if (static::$types === null) {
            $upload = config('varbox.bindings.services.upload_service', UploadService::class);

            static::$types = [
                $upload::getImageType() => 'Image',
                $upload::getVideoType() => 'Video',
                $upload::getAudioType() => 'Audio',
                $upload::getFileType() => 'File',
            ];
        }

        return static::$types;
    }

    /**
     * Create a fully qualified upload column in a database table.
     *
     * @param string $name
     * @param Blueprint $table
     */
    public static function column($name, Blueprint $table)
    {
        $table->string($name)->nullable();

        $table->foreign($name)
            ->references('full_path')
            ->on((new static)->getTable())
            ->onDelete('restrict')
            ->onUpdate('cascade');
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('upload')
            ->withEntityName($this->original_name)
            ->withEntityUrl(route('admin.uploads.index', [
                'search' => $this->original_name
            ]));
    }

    /**
     * Get the heading columns for the csv.
     *
     * @return array
     */
    public function getCsvColumns()
    {
        return [
            'Name', 'Source', 'Type', 'Size', 'Mime', 'Created At', 'Last Modified At',
        ];
    }

    /**
     * Get the values for a row in the csv.
     *
     * @return array
     */
    public function toCsvArray()
    {
        return [
            $this->original_name,
            uploaded($this->full_path)->url(),
            $this->type,
            $this->size_mb . ' MB',
            $this->mime,
            $this->created_at->format('Y-m-d H:i:s'),
            $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
