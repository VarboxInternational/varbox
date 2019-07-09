<?php

namespace Varbox\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;
use Varbox\Contracts\ErrorModelContract;
use Varbox\Traits\IsCacheable;

class Error extends Model implements ErrorModelContract
{
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'errors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'code',
        'url',
        'message',
        'occurrences',
        'file',
        'line',
        'trace',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Error $error) {
            if ($error->exists) {
                $error->occurrences++;
            }
        });
    }

    /**
     * Determine if an error should be saved to the database.
     *
     * @return bool
     */
    public function shouldSaveError()
    {
        return config('varbox.errors.enabled', true) === true;
    }

    /**
     * Store the registered error in the database.
     *
     * @param Exception $exception
     * @return void
     */
    public function saveError(Exception $exception)
    {
        if (!$this->shouldSaveError()) {
            return;
        }

        $type = get_class($exception);
        $code = $this->getParsedErrorCode($exception);
        $message = $exception->getMessage();
        $url = url()->current();

        static::updateOrCreate([
            'type' => $type,
            'code' => $code,
            'message' => $message,
            'url' => $url,
        ], [
            'type' => $type,
            'code' => $code,
            'message' => $message,
            'url' => $url,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Attempt to clean old errors.
     *
     * Errors qualify as being old if:
     * "created_at" field is smaller than the current date minus the number of days set in the
     * "old_threshold" key of /config/varbox/errors.php file.
     *
     * @return void
     */
    public static function deleteOld()
    {
        if (($days = (int)config('varbox.errors.old_threshold', 30)) && $days > 0) {
            static::where('created_at', '<', today()->subDays($days))->delete();
        }
    }

    /**
     * Get the actual error code.
     *
     * @param Exception $exception
     * @return int
     */
    protected function getParsedErrorCode(Exception $exception)
    {
        $code = $exception->getCode();

        if ($exception instanceof ModelNotFoundException) {
            $code = 404;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
        }

        return $code;
    }
}