<?php

namespace Varbox\Traits;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory as HasIlluminateFactory;

trait HasFactory
{
    use HasIlluminateFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        $model = Str::after(get_called_class(), 'Varbox\\Models\\');
        $class = "Database\Factories\\{$model}Factory";

        if (class_exists($class)) {
            return Factory::factoryForModel($model);
        }

        return Factory::factoryForModel(get_called_class());
    }
}
