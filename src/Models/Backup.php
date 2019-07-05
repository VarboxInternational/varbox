<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;
use Varbox\Contracts\BackupModelContract;

class Backup extends Model implements BackupModelContract
{
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'backups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'date',
        'size',
        'disk',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
    ];

    /**
     * Get the size in megabytes.
     *
     * @return string
     */
    public function getSizeInMbAttribute()
    {
        return number_format($this->attributes['size'] / pow(1024, 2), 2);
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Determine if the current backup is on the "local" filesystem driver.
     * Please note that we're talking about the filesystem "DRIVER", not the "DISK".
     *
     * @return bool
     */
    public function local()
    {
        return strtolower(config("filesystems.disks.{$this->disk}.driver")) === 'local';
    }

    /**
     * Download a backup zip archive from any storage driver.
     *
     * @return int|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download()
    {
        if ($this->local()) {
            return response()->download(
                Storage::disk($this->disk)->getDriver()->getAdapter()->applyPathPrefix($this->path)
            );
        }

        Storage::disk($this->disk)->setVisibility($this->path, 'public');

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . basename($this->path));
        header("Content-Type: application/zip");

        $file = readfile(Storage::disk($this->disk)->url($this->path));

        Storage::disk($this->disk)->setVisibility($this->path, 'private');

        return $file;
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('backup')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.backups.index'));
    }
}