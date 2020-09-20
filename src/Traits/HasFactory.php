<?php

namespace Varbox\Traits;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory as HasIlluminateFactory;

trait HasFactory
{
    use HasIlluminateFactory;

    /**
     * Get a new factory instance for the model.
     *
     * @param mixed $parameters
     * @return Factory
     */
    public static function factory(...$parameters)
    {
        $model = Str::after(get_called_class(), 'Varbox\\Models\\');
        $class = "Database\Factories\\{$model}Factory";

        if (static::newFactory()) {
            $factory = static::newFactory();
        } elseif (class_exists($class)) {
            $factory = Factory::factoryForModel($model);
        } else {
            $factory = Factory::factoryForModel(get_called_class());
        }

        return $factory
            ->count(is_numeric($parameters[0] ?? null) ? $parameters[0] : null)
            ->state(is_array($parameters[0] ?? null) ? $parameters[0] : ($parameters[1] ?? []));
    }
}
