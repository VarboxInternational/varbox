<?php

namespace Varbox\Sys\Models;

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
     * Store the registered error in the database.
     *
     * @param Exception $exception
     * @return void
     */
    public function saveError(Exception $exception)
    {
        $type = get_class($exception);
        $code = $exception->getCode();
        $message = $exception->getMessage();
        $url = url()->current();

        if ($exception instanceof ModelNotFoundException) {
            $code = 404;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
        }

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
}