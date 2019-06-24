<?php

namespace Varbox\Sniffers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionException;
use ReflectionMethod;
use SplFileObject;

class ModelSniffer
{
    /**
     * List of all relations defined on a model class.
     *
     * @var array
     */
    private $relations = [];

    /**
     * Laravel's available relation types (classes|methods).
     *
     * @var array
     */
    private $relationTypes = [
        'hasOne',
        'hasMany',
        'hasManyThrough',
        'belongsTo',
        'belongsToMany',
        'morphOne',
        'morphMany',
        'morphTo',
        'morphToMany'
    ];

    /**
     * Get all the defined model class relations.
     * Not just the eager loaded ones, present in the $relations Eloquent property.
     *
     * @param Model $model
     * @return array
     * @throws ReflectionException
     */
    public function getAllRelations($model)
    {
        foreach (get_class_methods($model) as $method) {
            if (!method_exists(Model::class, $method)) {
                $reflection = new ReflectionMethod($model, $method);
                $file = new SplFileObject($reflection->getFileName());
                $code = '';

                $file->seek($reflection->getStartLine() - 1);

                while ($file->key() < $reflection->getEndLine()) {
                    $code .= $file->current();
                    $file->next();
                }

                $code = trim(preg_replace('/\s\s+/', '', $code));
                $begin = strpos($code, 'function(');
                $code = substr($code, $begin, strrpos($code, '}') - $begin + 1);

                foreach ($this->relationTypes as $type) {
                    if (stripos($code, '$this->' . $type . '(')) {
                        $relation = $model->$method();

                        if ($relation instanceof Relation) {
                            $this->relations[$method] = [
                                'type' => get_class($relation),
                                'model' => $relation->getRelated(),
                                'original' => $relation->getParent(),
                            ];
                        }
                    }
                }
            }
        }

        return $this->relations;
    }
}
