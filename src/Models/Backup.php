<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Varbox\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Varbox\Contracts\BackupModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Backup extends Model implements BackupModelContract
{
    use HasFactory;
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
    public function isLocallyStored()
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
        if ($this->isLocallyStored()) {
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
     * Delete a backup's database record and corresponding storage file.
     *
     * @throws \Exception
     * @return void
     */
    public function deleteRecordAndFile()
    {
        $filesystem = Storage::disk($this->disk);

        if ($filesystem->exists($this->path)) {
            $filesystem->delete($this->path);
        }

        $this->delete();
    }

    /**
     * Delete all backup database records and their corresponding storage file.
     *
     * @return void
     */
    public static function deleteAll()
    {
        foreach (static::all() as $backup) {
            $backup->deleteRecordAndFile();
        }
    }

    /**
     * Attempt to delete old backup database records and their corresponding storage files.
     *
     * Backups qualify as being old if:
     * "date" field is smaller than the current date minus the number of days set in the
     * "old_threshold" key of /config/varbox/backup.php file.
     *
     * @return void
     */
    public static function deleteOld()
    {
        if (($days = (int)config('varbox.backup.old_threshold', 30)) && $days > 0) {
            $backups = static::where('date', '<', today()->subDays($days))->get();

            foreach ($backups as $backup) {
                $backup->deleteRecordAndFile();
            }
        }
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
            ->withEntityUrl(route('admin.backups.index', [
                'search' => $this->name,
                'start_date' => $this->date->format('Y-m-d'),
                'end_date' => $this->date->format('Y-m-d')
            ]));
    }
}
